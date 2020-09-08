#!/usr/bin/env python
# -*- coding: utf-8 -*-
from sys import argv
from tkinter.filedialog import *
from tkinter.messagebox import *
import numpy
import json
import base64
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
from ukrstemmer import UkrainianStemmer
from lsa import LSA

script, externalKey = argv

start_time = datetime.now()

stem = 'russian'
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
sourceData = r.hgetall("moodle_analytical_database_lsa")
sourceData = decode_redis(sourceData)
resultStatus = 0
resultParam = ''

for key in sourceData:
    if key == externalKey:
        item = json.loads(base64.b64decode(sourceData[key]).decode('utf-8'))

        docs = [
            item['questionText'],
            item['pageText']
        ]

        # вывод результата анализа текста
        obj = LSA(docs, stem, stopwords)
        obj.STart()
        resultStatus = obj.getResultStatus()
        resultParam = obj.getResultParam()

        r.hdel("moodle_analytical_database_lsa", key)

print(str(resultParam) + '|' + str(resultStatus))