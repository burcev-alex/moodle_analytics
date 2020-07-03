<!DOCTYPE html>
<html>
<head>
    <title>Рекоммендации по изучению темы</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<style>
    body { font-family: DejaVu Sans, sans-serif; }
  </style>
<body>
    <div style="width: 100%; max-width: 960px; margin: auto">
        <table width="100%">
            <tr style="border-bottom: 1px solid #000000">
                <td><h2>Тестовое задание</h2></td>
            <td style="text-align: right"><h3># {{ $quizId }}</h3></td>
            </tr>
            <tr>
                <td>
                    <strong>Курс:</strong><br>
                    {{ $courseName }}
                </td>
                <td style="text-align: right">
                    <strong>Дата:</strong><br>
                    03.07.2020<br><br>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <h3>Вопросы</h3>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <table width="100%" cellpadding="0" cellspacing="0" border="1">
                        <tbody>
                            @foreach ($questions as $question)
                                <tr style="background-color: #eee">
                                    <td style="text-align: left; padding: 5px 10px;" colspan="2">{{ $question['name'] }}</td>
                                </tr>
                                @foreach ($question['pages'] as $key=>$page)
                                    <tr>
                                        <td style="text-align: left; padding: 5px 10px; width:5%;">{{ $key }}</td>
                                        <td style="text-align: left; padding: 5px 10px;"><a href="{{ $page['link'] }}" target="_blank">{{ $page['title'] }}</a></td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>