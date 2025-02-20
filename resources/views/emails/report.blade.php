<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Etiquette - Report</title>
</head>
<body>
    <h1>Hello!</h1>
    @if(isset($feed_message))
        <p> {{$consultant_name . 'danışanının oluşturduğu ' . $record_type_name . ' kaydı için rapor bildirildi.'}} </p>
        <p> Rapor Adı </p>
        <p>{{ $report_name }}</p>
        <p> Akış Mesajı </p>
        <p>{{ $feed_message }}</p>
    @else
        <p> {{$consultant_name . 'danışanının oluşturduğu ' . $portfolio_title . ' portföyü için rapor bildirildi.'}} </p>
        <p> Rapor Adı </p>
        <p>{{ $report_name }}</p>
    @endif

    <p>Best regards,<br>
</body>
</html>