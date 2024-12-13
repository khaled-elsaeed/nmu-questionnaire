<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <meta name="x-apple-disable-message-reformatting">
    <meta content="IE=edge" http-equiv="X-UA-Compatible">
    <meta content="telephone=no" name="format-detection">
    <title>بيانات تسجيل الدخول - نظام الاستبيانات</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Nunito', Arial, sans-serif;
            color: #666666;
            line-height: 1.8;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            direction: rtl;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            border: 1px solid #ddd;
        }
        .header {
            background-color: #931a23;
            padding: 30px;
            text-align: center;
            color: #ffffff;
        }
        .header img {
            width: 80px;
            height: auto;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 26px;
            font-weight: 700;
            margin: 0;
            text-transform: uppercase;
        }
        .content {
            padding: 25px;
        }
        .button {
            display: inline-block;
            padding: 14px 28px;
            font-size: 18px;
            font-weight: 600;
            color: #ffffff;
            background: #1e2f49;
            text-decoration: none !important;
            border-radius: 8px;
            margin-top: 20px;
            transition: background-color 0.3s ease, transform 0.3s ease;
            text-align: center;
            width: 60%;
        }
        .button:hover {
            background-color: #16243a;
            transform: translateY(-3px);
        }
        h2, h3 {
            color: #931a23;
            margin-top: 0;
            font-weight: 700;
        }
        p {
            margin: 0 0 18px;
            font-size: 16px;
            color: #333333;
        }
        .footer {
            background-color: #f9f9f9;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #666666;
            border-top: 1px solid #ddd;
            border-radius: 0 0 12px 12px;
        }
        .footer a {
            color: #931a23;
            text-decoration: none;
        }
        .footer p {
            margin: 5px 0;
        }
        @media (max-width: 600px) {
            .container {
                padding: 0;
                box-shadow: none;
                border: none;
            }
            .header, .footer {
                padding: 15px;
            }
            .content {
                padding: 15px;
            }
            .button {
                padding: 12px;
                font-size: 16px;
            }
            h2, h3 {
                font-size: 22px;
            }
            p {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img alt="شعار الاستبيانات" src="https://es.nmu.edu.eg/questionnaire/images/logo.png" title="شعار الاستبيانات">
            <h1>نظام الاستبيانات</h1>
        </div>
        <div class="content">
            <img src="https://es.nmu.edu.eg/questionnaire/images/email/account_credintials.svg" alt="الحساب" title="الحساب">

            <h2>مرحباً بك في نظام الاستبيانات!</h2>
            <p>
                طلابنا الأعزاء،<br>
                لقد تم إنشاء نظام استبيانات جديد لاستطلاع آراء الطلاب بخصوص المقررات المسجلة في الفصل الدراسي الحالي. نرجو منكم اتباع التعليمات التالية:
            </p>
            <ol>
    <li>تسجيل الدخول إلى النظام باستخدام اسم المستخدم (البريد الإلكتروني) وكلمة المرور المرفقة.</li>
    <li>بعد تسجيل الدخول، اختر الاستبيان من واجهة الاستبيانات عبر الضغط على زر "الانتقال إلى الاستبيان" المقابل لكل استبيان.</li>
    <li>ملء الاستبيان وفقًا لمتطلبات كل سؤال.</li>
    <li>بعد مراجعة الإجابات، اضغط على زر "إرسال الإجابات" لتسليم الاستبيان.</li>
    <li>يرجى من جميع الطلاب تعبئة الاستبيانات المقررة عليهم في النظام.</li>
</ol>

            <p>
                - البريد الإلكتروني: {{ $email }}<br>
                - كلمة المرور: {{ $password }}
            </p>
            <p>
                يُرجى النقر على الزر أدناه للدخول إلى النظام والبدء في الإجابة على الاستبيانات المطلوبة:
            </p>
            <p>
                <a href="https://es.nmu.edu.eg/questionnaire/" target="_blank" class="button">تسجيل الدخول</a>
            </p>
           
        </div>
        <div class="footer">
            <p>للاستفسار، يرجى التواصل عبر البريد الإلكتروني <a href="mailto:info@nmu.edu.eg">info@nmu.edu.eg</a>.</p>
            <p>&copy; فريق نظام الاستبيانات بجامعة NMU. جميع الحقوق محفوظة.</p>
        </div>
    </div>
</body>
</html>
