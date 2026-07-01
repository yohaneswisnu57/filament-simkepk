<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Certificate Approval</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 5px 20px 20px 20px;
            color: #000;
            line-height: 1.4;
            font-size: 14px;
        }
        .header-table {
            margin: 0 auto 20px auto;
            width: auto;
        }
        .header-logo {
            vertical-align: top;
        }
        .header-logo img {
            width: 90px;
            height: auto;
            margin-left: -10px;
        }
        .header-text {
            text-align: center;
            font-family: 'Times New Roman', Times, serif;
        }
        .header-text h1 { font-size: 14px; font-weight: bold; margin: 2px 0; }
        .header-text h2 { font-size: 15px; font-weight: bold; margin: 2px 0; }
        .header-text h3 { font-size: 16px; font-weight: bold; margin: 2px 0; }
        .header-address {
            font-size: 11px;
            margin-top: 5px;
            font-family: 'Times New Roman', Times, serif;
        }
        .divider {
            border-top: 3px solid #000;
            margin-bottom: 20px;
        }
        .title {
            text-align: center;
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 5px;
        }
        .ref {
            text-align: center;
            font-size: 14px;
            margin-bottom: 30px;
        }
        .content-table {
            width: 100%;
            margin-bottom: 30px;
            border-collapse: collapse;
        }
        .content-table td {
            vertical-align: top;
            padding: 5px 0;
        }
        .label-col {
            width: 30%;
        }
        .colon-col {
            width: 2%;
        }
        .value-col {
            width: 68%;
        }
        .member-list {
            margin: 0;
            padding-left: 15px;
        }
        .member-list li {
            margin-bottom: 3px;
        }
        .text-paragraph {
            text-align: justify;
            margin-bottom: 15px;
        }
        .signature-section {
            margin-top: 40px;
            text-align: right;
            padding-right: 50px;
        }
        .signature-box {
            display: inline-block;
            text-align: center;
        }
        .signature-box .date {
            margin-bottom: -1px;
        }
        .signature-box .name {
            margin-top:1px;
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td class="header-logo">
                @php
                    $logoPath = public_path('images/logo-ukwms.png');
                    $logoData = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : '';
                @endphp
                @if($logoData)
                    <img src="data:image/png;base64,{{ $logoData }}" alt="Logo">
                @endif
            </td>
            <td class="header-text">
                <h1>YAYASAN WIDYA MANDALA SURABAYA</h1>
                <h2>UNIVERSITAS KATOLIK WIDYA MANDALA SURABAYA</h2>
                <h3>FAKULTAS KEDOKTERAN</h3>
                <h3>KOMISI ETIK PENELITIAN KESEHATAN</h3>
                <div class="header-address">
                    Universitas Katolik Widya Mandala Tower Barat Lt. 6, Jl. Raya Kalisari Selatan No.1, Pakuwon City, Surabaya<br>
                    Telp.(031) 99005299 ext. 10656, Fax.(031) 99005278, http://fk.ukwms.ac.id, https://simkepk.ukwms.ac.id<br>
                    email: kepk.fkukwms@gmail.com; kepk.fk@ukwms.ac.id
                </div>
            </td>
        </tr>
    </table>

    <div class="divider"></div>

    <div class="title">HEALTH RESEARCH ETHICS COMMITTEE APPROVAL</div>
    <div class="ref">Ref: {{ $certificate->certificate_number ?? '-' }}</div>

    <table class="content-table">
        <tr>
            <td class="label-col">Title of the Research Protocol</td>
            <td class="colon-col">:</td>
            <td class="value-col">{{ $protocol->perihal_pengajuan }}</td>
        </tr>
        <tr>
            <td class="label-col">Principle Investigator</td>
            <td class="colon-col">:</td>
            <td class="value-col">{{ $protocol->user->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label-col">Member of Investigator</td>
            <td class="colon-col">:</td>
            <td class="value-col">
                @if(!empty($certificate->members) && count($certificate->members) > 0)
                    <ul class="member-list">
                        @foreach($certificate->members as $member)
                            <li>{{ $member['name'] ?? '' }}</li>
                        @endforeach
                    </ul>
                @else
                    -
                @endif
            </td>
        </tr>
        <tr>
            <td class="label-col">Date of approval</td>
            <td class="colon-col">:</td>
            <td class="value-col">
                {{ $certificate->approval_date ? $certificate->approval_date->format('d F Y') : date('d F Y') }}<br>
                <small>(Valid for one year beginning from the date of approval)</small>
            </td>
        </tr>
        <tr>
            <td class="label-col">Institution(s)/Place(s)<br>of research</td>
            <td class="colon-col">:</td>
            <td class="value-col">{{ $certificate->institution_name ?? '-' }}</td>
        </tr>
    </table>

    <div class="text-paragraph">
        The Health Research Ethics Committee (HREC) states that the above protocol meets the ethical principles outlined in CIOMS and WHO (2016) International Ethical Guidelines for Health-related Research Involving Humans, CIOMS Geneva, and therefore can be carried out.
    </div>

    <div class="text-paragraph">
        The investigator(s) is/are obliged to submit progress report, report of any serious adverse event(s) and a final report upon completion of the research. The Health Research Ethics Committee reserves the right to monitor the research activities at any time.
    </div>

    <div class="signature-section">
        <div class="signature-box" style="float: right; width: 300px; text-align: center;">
            <div class="date" style="text-align: center; margin-bottom: -5px; font-size: 15px;">Surabaya, {{ $certificate->approval_date ? $certificate->approval_date->format('d F Y') : date('d F Y') }}</div>
            
            @php
                $sigPath = public_path('images/tandatanga-chairperson.png');
                $sigData = file_exists($sigPath) ? base64_encode(file_get_contents($sigPath)) : '';
            @endphp

            <div class="signature-images" style="margin: 0 0 5px 0;">
                @if($sigData)
                    <img src="data:image/png;base64,{{ $sigData }}" style="width: 200px; height: auto;" alt="Signature and Stamp">
                @endif
            </div>

            <div class="name" style="font-weight: bold; font-size: 15px; margin-top: -15px;">Caroline, S.Si., M.Si., Apt</div>
            <div class="title" style="font-weight: bold; font-size: 15px;">Chairperson</div>
        </div>
        <div style="clear: both;"></div>
    </div>

</body>
</html>
