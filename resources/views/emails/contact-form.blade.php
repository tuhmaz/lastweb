<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>رسالة جديدة من نموذج الاتصال</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #eee;
        }
        .content {
            margin-bottom: 30px;
        }
        .field {
            margin-bottom: 15px;
        }
        .label {
            font-weight: bold;
            color: #696cff;
            margin-bottom: 5px;
        }
        .value {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #eee;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #eee;
            color: #666;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>رسالة جديدة من نموذج الاتصال</h1>
            <p>تم استلام رسالة جديدة من موقع {{ config('settings.site_name') }}</p>
        </div>

        <div class="content">
            <div class="field">
                <div class="label">الاسم:</div>
                <div class="value">{{ $data['name'] }}</div>
            </div>

            <div class="field">
                <div class="label">البريد الإلكتروني:</div>
                <div class="value">{{ $data['email'] }}</div>
            </div>

            @if(isset($data['phone']) && !empty($data['phone']))
            <div class="field">
                <div class="label">رقم الهاتف:</div>
                <div class="value">{{ $data['phone'] }}</div>
            </div>
            @endif

            <div class="field">
                <div class="label">الموضوع:</div>
                <div class="value">{{ $data['subject'] }}</div>
            </div>

            <div class="field">
                <div class="label">الرسالة:</div>
                <div class="value">{{ $data['message'] }}</div>
            </div>
        </div>

        <div class="footer">
            <p>هذه الرسالة تم إرسالها تلقائياً من نموذج الاتصال في موقع {{ config('settings.site_name') }}</p>
            <p>© {{ date('Y') }} موقع {{ config('settings.site_name') }}. جميع الحقوق محفوظة</p>
        </div>
    </div>
</body>
</html>
