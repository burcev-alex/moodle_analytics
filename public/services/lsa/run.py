#!/usr/bin/env python
# -*- coding: utf-8 -*-

import os
import sys
from tkinter.filedialog import *
from tkinter.messagebox import *
import numpy
import json
import base64
import re
from io import StringIO
import numpy as np
from numpy import *
from sklearn.cluster import KMeans
import nltk
import redis
import scipy
from nltk.corpus import brown
from datetime import datetime
import time

script, nameQueue, keyQueue = sys.argv

start_time = datetime.now()

class UkrainianStemmer():
    def __init__(self, word):
        self.word = word
        self.vowel = r'аеиоуюяіїє'  # http://uk.wikipedia.org/wiki/Голосний_звук
        self.perfectiveground = r'(ив|ивши|ившись|ыв|ывши|ывшись((?<=[ая])(в|вши|вшись)))$'
        # http://uk.wikipedia.org/wiki/Рефлексивне_дієслово
        self.reflexive = r'(с[яьи])$'
        # http://uk.wikipedia.org/wiki/Прикметник + http://wapedia.mobi/uk/Прикметник
        self.adjective = r'(ими|ій|ий|а|е|ова|ове|ів|є|їй|єє|еє|я|ім|ем|им|ім|их|іх|ою|йми|іми|у|ю|ого|ому|ої)$'
        # http://uk.wikipedia.org/wiki/Дієприкметник
        self.participle = r'(ий|ого|ому|им|ім|а|ій|у|ою|ій|і|их|йми|их)$'
        # http://uk.wikipedia.org/wiki/Дієслово
        self.verb = r'(сь|ся|ив|ать|ять|у|ю|ав|али|учи|ячи|вши|ши|е|ме|ати|яти|є)$'
        # http://uk.wikipedia.org/wiki/Іменник
        self.noun = r'(а|ев|ов|е|ями|ами|еи|и|ей|ой|ий|й|иям|ям|ием|ем|ам|ом|о|у|ах|иях|ях|ы|ь|ию|ью|ю|ия|ья|я|і|ові|ї|ею|єю|ою|є|еві|ем|єм|ів|їв|ю)$'
        self.rvre = r'[аеиоуюяіїє]'
        self.derivational = r'[^аеиоуюяіїє][аеиоуюяіїє]+[^аеиоуюяіїє]+[аеиоуюяіїє].*(?<=о)сть?$'
        self.RV = ''

    def ukstemmer_search_preprocess(self, word):
        word = word.lower()
        word = word.replace("'", "")
        word = word.replace("ё", "е")
        word = word.replace("ъ", "ї")
        return word

    def s(self, st, reg, to):
        orig = st
        self.RV = re.sub(reg, to, st)
        return (orig != self.RV)

    def stem_word(self):
        word = self.ukstemmer_search_preprocess(self.word)
        if not re.search('[аеиоуюяіїє]', word):
            stem = word
        else:
            p = re.search(self.rvre, word)
            start = word[0:p.span()[1]]
            self.RV = word[p.span()[1]:]

            # Step 1
            if not self.s(self.RV, self.perfectiveground, ''):

                self.s(self.RV, self.reflexive, '')
                if self.s(self.RV, self.adjective, ''):
                    self.s(self.RV, self.participle, '')
                else:
                    if not self.s(self.RV, self.verb, ''):
                        self.s(self.RV, self.noun, '')
            # Step 2
            self.s(self.RV, 'и$', '')

            # Step 3
            if re.search(self.derivational, self.RV):
                self.s(self.RV, 'ость$', '')

            # Step 4
            if self.s(self.RV, 'ь$', ''):
                self.s(self.RV, 'ейше?$', '')
                self.s(self.RV, 'нн$', u'н')

            stem = start + self.RV
        return stem

class LSA:
    def __init__(self, docs, stem, stopwords):
        self.docs = docs
        self.status = 0
        self.doc = [w for w in docs]
        self.stem = stem
        self.stopwords = stopwords

    def STart(self):
        #print('Исходные документы\n\n')
        #for k, v in enumerate(docs):
        #    print('Док--%u | Текст-%s \n\n' % (k, v))
        t = " "
        word = nltk.word_tokenize((' ').join(self.doc))
        stopword = [UkrainianStemmer(w).stem_word().lower() for w in self.stopwords]
        return self.WordStopDoc(t, stopword)

    def word_1(self):
        word = nltk.word_tokenize((' ').join(self.doc))
        n = [UkrainianStemmer(w).stem_word().lower()
             for w in word if len(w) > 1 and w.isalpha()]
        stopword = [UkrainianStemmer(w).stem_word().lower() for w in self.stopwords]
        fdist = nltk.FreqDist(n)
        t = fdist.hapaxes()
        return self.WordStopDoc(t, stopword)

    def WordStopDoc(self, t, stopword):
        d = {}
        c = []
        p = {}
        for i in range(0, len(self.doc)):
            word = nltk.word_tokenize(self.doc[i])
            word_stem = [UkrainianStemmer(w).stem_word().lower()
                         for w in word if len(w) > 1 and w.isalpha()]
            word_stop = [w for w in word_stem if w not in stopword]
            words = [w for w in word_stop if w not in t]
            p[i] = [w for w in words]
            for w in words:
                if w not in c:
                    c.append(w)
                    d[w] = [i]
                elif w in c:
                    d[w] = d[w]+[i]
        return self.Create_Matrix(d, c, p)

    def Create_Matrix(self, d, c, p):
        a = len(c)
        b = len(self.doc)
        A = numpy.zeros([a, b])
        c.sort()
        for i, k in enumerate(c):
            for j in d[k]:
                A[i, j] += 1
        return self.Analitik_Matrix(A, c, p)

    def Analitik_Matrix(self, A, c, p):
        wdoc = sum(A, axis=0)
        pp = []
        q = -1
        for w in wdoc:
            q = q+1
            if w == 0:
                pp.append(q)
        if len(pp) != 0:
            for k in pp:
                self.doc.pop(k)
            self.word_1()
        elif len(pp) == 0:
            rows, cols = A.shape
            nn = []
            for i, row in enumerate(A):
                st = (c[i], row)
                stt = sum(row)
                nn.append(stt)
            return self.TF_IDF(A, c, p)

    def TF_IDF(self, A, c, p):
        wpd = sum(A, axis=0)
        dpw = sum(asarray(A > 0, 'i'), axis=1)
        rows, cols = A.shape
        for i in range(rows):
            for j in range(cols):
                m = float(A[i, j])/wpd[j]
                n = log(float(cols) / dpw[i])
                A[i, j] = round(n*m, 2)
        gg = []
        for i, row in enumerate(A):
            st = (c[i], row)
            stt = sum(row)
            gg.append(stt)
        l = gg.index(max(gg))
        return self.U_S_Vt(A, c, p, l)

    def U_S_Vt(self, A, c, p, l):
        U, S, Vt = numpy.linalg.svd(A)
        rows, cols = U.shape
        for j in range(0, cols):
            for i in range(0, rows):
                U[i, j] = round(U[i, j], 4)
        for i, row in enumerate(U):
            st = (c[i], row[0:2])
        kt = l
        wordd = c[l]
        res1 = -1*U[:, 0:1]
        wx = res1[kt]
        res2 = -1*U[:, 1:2]
        wy = res2[kt]
        Z = np.diag(S)
        rows, cols = Vt.shape
        for j in range(0, cols):
            for i in range(0, rows):
                Vt[i, j] = round(Vt[i, j], 4)
        st = (-1*Vt[0:2, :])
        res3 = (-1*Vt[0:1, :])
        res4 = (-1*Vt[1:2, :])
        X = numpy.dot(U[:, 0:2], Z[0:2, 0:2])
        Y = numpy.dot(X, Vt[0:2, :])
        rows, cols = Y.shape
        return self.Word_Distance_Document(res1, wx, res2, wy, res3, res4, Vt, p, c, Z, U)

    def Word_Distance_Document(self, res1, wx, res2, wy, res3, res4, Vt, p, c, Z, U):
        xx, yy = -1 * Vt[0:2, :]
        Q = np.matrix(U)
        UU = Q.T
        rows, cols = UU.shape
        a = cols
        b = cols
        B = numpy.zeros([a, b])
        for i in range(0, cols):
            for j in range(0, cols):
                xxi, yyi = -1 * UU[0:2, i]
                xxi1, yyi1 = -1 * UU[0:2, j]
                param3 = float(xxi*xxi1+yyi*yyi1)
                param4 = float(sqrt((xxi*xxi+yyi*yyi)*(xxi1*xxi1+yyi1*yyi1)))
                if param4 != 0:
                    B[i, j] = round(param3/param4, 6)
                else:
                    B[i, j] = 0
        arts = []
        #print('Результаты анализа: Всего документов:%u. Осталось документов после исключения не связанных:%u\n' % (
        #    len(self.docs), len(self.doc)))
        if len(self.docs) > len(self.doc):
            #print(" Оставшиеся документы после исключения не связанных:")
            #print('\n')
            for k, v in enumerate(self.doc):
                ww = 'Док.№ - %i. Text -%s' % (k, v)
                #print(ww)
                #print('\n')
        for k in range(0, len(self.doc)):
            ax, ay = xx[k], yy[k]
            dx, dy = float(wx - ax), float(wy - ay)
            dist = float(sqrt(dx * dx + dy * dy))
            arts.append((k, p[k], round(dist, 3)))
        q = (sorted(arts, key=lambda a: a[2]))
        dd = []
        ddm = []
        aa = []
        bb = []
        for i in range(1, len(self.doc)):
            cos1 = q[i][2]
            cos2 = q[i-1][2]
            qq = round(float(cos1-cos2), 3)
            tt = [(q[i-1])[0], (q[i])[0]]
            dd.append(tt)
            ddm.append(qq)
        for w in range(0, len(dd)):
            i = ddm.index(min(ddm))
            aa.append(dd[i])
            bb.append(ddm[i])
            del dd[i]
            del ddm[i]

        resultActial = 0
        for i in range(0, len(aa)):
            if len([w for w in p[aa[i][0]]if w in p[aa[i][1]]]) != 0:
                zz = [w for w in p[aa[i][0]]if w in p[aa[i][1]]]
            else:
                zz = ['нет общих слов']
            cs = []
            for w in zz:
                if w not in cs:
                    cs.append(w)
            sc = "Евклидова мера расстояния "
            tr = '№ Док %s- %s-%s -Общие слова -%s' % (aa[i], bb[i], sc, cs)
            if (float(bb[i]) >= 0.35) & (float((len(cs)/len(self.doc[0].split())*100)) > 30):
                resultActial = 1
            else:
                resultActial = 0

            #print(tr)
            #print("------ \n")

        self.status = resultActial

        return resultActial

    def out_green(self, text):
        print("\033[32m {}" .format(text))
        print("\033[0m")

    def out_red(self, text):
        print("\033[31m {}" .format(text))
        print("\033[0m")

    def out_yellow(self, text):
        print("\033[33m {}" .format(text))
        print("\033[0m")

    def out_blue(self, text):
        print("\033[34m {}" .format(text))
        print("\033[0m")

    def getResult(self):
        if self.status == 1:
            print("SUCCESS")
        else:
            print("ERROR")

        return self.status


stem = 'russian'
#stopwords = nltk.corpus.stopwords.words(stem)
stopwords = ["a", "б", "в", "г", "е", "ж", "з", "м", "т", "у", "я", "є", "і", "аж", "ви", "де", "до", "за", "зі", "ми", "на", "не", "ну", "нх", "ні", "по", "та", "ти", "то", "ту", "ті", "це", "цю", "ця", "ці", "чи", "ще", "що", "як", "їй", "їм", "їх", "її", "або", "але", "ало", "без", "був", "вам", "вас", "ваш", "вже", "все", "всю", "вся", "від", "він", "два", "дві", "для", "ким", "мож", "моя", "моє", "мої", "міг", "між", "мій", "нам", "нас", "наш", "нею", "неї", "них", "ніж", "ній", "ось", "при", "про", "пір", "раз", "рік", "сам", "сих", "так", "там", "теж", "тим", "тих", "той", "тою", "три", "тут", "хоч", "хто", "цей", "цим", "цих", "час", "щоб", "яка", "які", "адже", "буде", "буду", "будь", "була", "були", "було", "бути", "вами", "ваша", "ваше", "ваші", "весь", "вниз", "вона", "вони", "воно", "всею", "всім", "всіх", "втім", "геть", "далі", "зате", "його", "йому", "каже", "кого", "коли", "кому", "крім", "куди", "лише", "мало", "мене", "мені", "мною", "нами", "наша", "наше", "наші", "ними", "ніби", "поки", "пора", "сама", "саме", "саму", "самі", "свою", "своє", "свої", "себе", "собі", "став", "така", "таке", "такі", "твоя", "твоє", "твій", "тебе", "тими", "тобі", "того", "тоді", "тому", "туди", "хоча", "хіба", "цими", "цієї", "інша", "інше", "інші", "буває", "будеш", "більш", "вгору", "внизу", "вісім", "кожен", "кожна", "кожне", "кожні", "краще", "ледве", "майже", "менше", "могти", "можна", "нього", "однак", "потім", "самим", "самих", "самій", "свого", "своєї", "своїх", "собою", "такий", "також", "тобою", "трохи", "усюди", "усіма", "хочеш", "цього", "цьому", "часто", "через", "якого", "іноді", "інший", "інших", "багато", "будемо", "будете", "будуть", "більше", "всього", "всьому", "далеко", "десять", "досить", "другий", "дійсно", "завжди", "звідси", "зовсім", "кругом", "кілька", "можуть", "навіть", "навіщо", "небудь", "низько", "ніколи", "нікуди", "нічого", "обидва", "одного", "однієї", "просто", "раніше", "раптом", "самими", "самого", "самому", "скрізь", "тільки", "близько", "важлива", "важливе", "важливі", "вдалині", "зайнята", "занадто", "значить", "навколо", "нарешті", "нерідко", "повинно", "посеред", "початку", "пізніше", "сказала", "сказати", "скільки", "спасибі", "частіше", "важливий", "зазвичай", "зайнятий", "звичайно", "здається", "найбільш", "недалеко", "особливо", "потрібно", "спочатку", "сьогодні", "численна", "численне", "численні", "відсотків", "звідусіль", "нещодавно", "численний", "будь-ласка", "безперервно"]

def decode_redis(src):
    if isinstance(src, list):
        rv = list()
        for key in src:
            rv.append(decode_redis(key))
        return rv
    elif isinstance(src, dict):
        rv = dict()
        for key in src:
            rv[key.decode()] = decode_redis(src[key])
        return rv
    elif isinstance(src, bytes):
        return src.decode()
    else:
        raise Exception("type not handled: " + type(src))


io = StringIO()
r = redis.Redis()
sourceData = r.hgetall(nameQueue)
sourceData = decode_redis(sourceData)
for key in sourceData:
    if keyQueue == key:
        item = json.loads(base64.b64decode(sourceData[key]).decode('utf-8'))
        #print(item)

        docs = [
            item['questionText'],
            item['pageText']
        ]

        # вывод результата анализа текста
        obj = LSA(docs, stem, stopwords)
        obj.STart()
        result = obj.getResult()

        def current_milli_time(): return int(round(time.time() * 1000))

        if result == 1:
            tmpStr = json.dumps(item).encode('utf8')
            strBase64 = base64.b64encode(tmpStr)
            r.hset('moodle_ml_database_notes', '{0}{1}'.format('note_', key), strBase64)

        r.hdel(nameQueue, key)

#print(datetime.now() - start_time)