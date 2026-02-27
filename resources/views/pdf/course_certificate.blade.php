<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Course Certificate</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 8mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: DejaVu Sans, sans-serif;
            color: #111111;
            background: #ececec;
        }

        .sheet {
            width: 281mm;
            height: 194mm;
            position: relative;
            overflow: hidden;
            background: #ececec;
            padding: 14mm 16mm 12mm;
            border: 0.35mm solid #d3d3d3;
        }

        .content {
            position: relative;
            z-index: 20;
            text-align: center;
        }

        .title {
            margin: 8mm 0 0;
            font-size: 34px;
            line-height: 1;
            letter-spacing: 2px;
            color: #172d6b;
            font-weight: 800;
        }

        .subtitle {
            margin: 1.5mm 0 8mm;
            font-size: 15px;
            letter-spacing: 1px;
            font-weight: 700;
            color: #000000;
            text-transform: uppercase;
        }

        .certifies {
            margin: 0;
            font-size: 8mm;
        }

        .teacher {
            margin: 3mm 0 2.5mm;
            font-size: 17mm;
            font-weight: 700;
            line-height: 1;
            color: #172d6b;
            font-family: "Brush Script MT", "Segoe Script", cursive;
        }

        .line {
            width: 56%;
            margin: 0 auto 3.5mm;
            border-top: 0.5mm solid #222;
        }

        .statement {
            width: 76%;
            margin: 0 auto;
            font-size: 5.6mm;
            line-height: 1.3;
            color: #151515;
        }

        .course-title {
            font-weight: 700;
            color: #172d6b;
        }

        .given {
            margin: 4mm 0 0;
            font-size: 6.5mm;
            font-weight: 600;
        }

        .cert-number {
            margin-top: 1.8mm;
            font-size: 3.4mm;
            color: #21304d;
        }

        .signatures {
            margin-top: 5.5mm;
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
        }

        .signatures td {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }

        .signature-line {
            width: 60%;
            margin: 0 auto 2.2mm;
            border-top: 0.55mm solid #222;
        }

        .sign-name {
            font-size: 6.2mm;
            font-weight: 600;
            color: #111;
        }

        .sign-role {
            font-size: 5.2mm;
            color: #111;
        }

        .shape {
            position: absolute;
            z-index: 5;
            background: #142d70;
        }

        .shape.right-a {
            width: 132mm;
            height: 11mm;
            right: -48mm;
            top: 29mm;
            transform: rotate(55deg);
        }

        .shape.right-b {
            width: 128mm;
            height: 7mm;
            right: -42mm;
            top: 33mm;
            background: #d09d1f;
            transform: rotate(55deg);
        }

        .shape.right-c {
            width: 95mm;
            height: 13mm;
            right: -44mm;
            top: 88mm;
            transform: rotate(110deg);
            background: #0c1f56;
        }

        .shape.right-d {
            width: 79mm;
            height: 7.5mm;
            right: -34mm;
            top: 93mm;
            transform: rotate(110deg);
            background: #d6a52a;
        }

        .shape.left-a {
            width: 111mm;
            height: 10mm;
            left: -47mm;
            bottom: 19mm;
            transform: rotate(38deg);
            background: #172d6b;
        }

        .shape.left-b {
            width: 104mm;
            height: 6.3mm;
            left: -41mm;
            bottom: 19mm;
            transform: rotate(38deg);
            background: #d8a62b;
        }
    </style>
</head>
<body>
    <div class="sheet">
        <div class="shape right-a"></div>
        <div class="shape right-b"></div>
        <div class="shape right-c"></div>
        <div class="shape right-d"></div>
        <div class="shape left-a"></div>
        <div class="shape left-b"></div>

        <div class="content">
            <h1 class="title">CERTIFICATE</h1>
            <div class="subtitle">OF COMPLETION</div>
            <p class="certifies">This certifies that</p>
            <p class="teacher">{{ $teacherName }}</p>
            <div class="line"></div>

            <p class="statement">
                has completed all necessary tasks and demonstrated the skills required for
                <span class="course-title">{{ $courseTitle }}</span>.
                The successful completion of this program reflects commitment to excellence and professional growth.
            </p>

            <p class="given">Given this {{ $issueDate }}</p>
            <p class="cert-number">Certificate No: {{ $certificateNumber }}</p>

            <table class="signatures">
                <tr>
                    <td>
                        <div class="signature-line"></div>
                        <div class="sign-name">Hour of Light</div>
                        <div class="sign-role">Senior Instructor</div>
                    </td>
                    <td>
                        <div class="signature-line"></div>
                        <div class="sign-name">Hour of Light Administration</div>
                        <div class="sign-role">Program Director</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
