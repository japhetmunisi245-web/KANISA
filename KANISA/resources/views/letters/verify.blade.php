<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UHAKIKI WA BARUA — E.A.G.T KIBURUGWA PHILADELPHIA</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #f1f5f9;
            color: #0f172a;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        .verify-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            max-width: 520px;
            width: 100%;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }
        .verify-header {
            background: linear-gradient(135deg, #1b1f3b 0%, #2e3458 100%);
            color: #ffffff;
            padding: 30px 24px;
            text-align: center;
            position: relative;
        }
        .verify-header .icon {
            font-size: 2.8rem;
            margin-bottom: 8px;
        }
        .verify-header h1 {
            font-size: 1.2rem;
            font-weight: 800;
            letter-spacing: 0.5px;
        }
        .verify-header p {
            font-size: 0.82rem;
            color: #94a3b8;
            margin-top: 4px;
        }
        .status-badge-container {
            margin-top: -20px;
            text-align: center;
        }
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 20px;
            border-radius: 999px;
            font-weight: 800;
            font-size: 0.88rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .status-valid { background: #dcfce7; color: #166534; border: 1.5px solid #86efac; }
        .status-invalid { background: #fee2e2; color: #991b1b; border: 1.5px solid #fca5a5; }

        .verify-body {
            padding: 28px 24px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px dashed #e2e8f0;
        }
        .info-row:last-child { border-bottom: none; }
        .info-label {
            font-size: 0.85rem;
            color: #64748b;
            font-weight: 600;
        }
        .info-value {
            font-size: 0.92rem;
            font-weight: 700;
            color: #0f172a;
            text-align: right;
        }
        .verify-footer {
            background: #f8fafc;
            padding: 16px 24px;
            text-align: center;
            font-size: 0.78rem;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>

    <div class="verify-card">
        <div class="verify-header">
            <div style="background:#ffffff;display:inline-block;padding:8px;border-radius:12px;margin-bottom:10px;">
                <img src="{{ asset('images/eagt_logo.png') }}" alt="EAGT Logo" style="max-height:55px;">
            </div>
            <h1>E.A.G.T KIBURUGWA PHILADELPHIA</h1>
            <p>DOCUMENT VERIFICATION SYSTEM</p>
        </div>

        @if($letter)
            <div class="status-badge-container">
                <div class="status-badge status-valid">
                    <i class="fa-solid fa-circle-check"></i> Status: VALID & VERIFIED
                </div>
            </div>

            <div class="verify-body">
                <div class="info-row">
                    <span class="info-label">Kumb. ya Barua (Ref)</span>
                    <span class="info-value" style="color:#6366f1;">{{ $letter->reference_number }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Aina ya Barua</span>
                    <span class="info-value">{{ $letter->letter_type }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Imeombwa Na</span>
                    <span class="info-value">{{ $letter->user->name ?? '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Kwayo / Kwa (Recipient)</span>
                    <span class="info-value">{{ $letter->recipient }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tarehe ya Kutolewa</span>
                    <span class="info-value">{{ \Carbon\Carbon::parse($letter->approved_at)->format('d/m/Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Mtoaji / Muweka Idhini</span>
                    <span class="info-value">REV. MAGDALENA MKINGA</span>
                </div>
            </div>
        @else
            <div class="status-badge-container">
                <div class="status-badge status-invalid">
                    <i class="fa-solid fa-circle-xmark"></i> Status: INVALID DOCUMENT
                </div>
            </div>

            <div class="verify-body" style="text-align:center;padding:40px 20px;">
                <p style="color:#ef4444;font-weight:700;font-size:1rem;margin-bottom:8px;">Barua Haimpatikani au Sio Halisi</p>
                <p style="color:#64748b;font-size:.85rem;">Token ya uhakiki haipo kwenye database ya kanisa. Tafadhali wasiliana na Uongozi wa Kanisa.</p>
            </div>
        @endif

        <div class="verify-footer">
            © {{ date('Y') }} E.A.G.T Kiburugwa Philadelphia Church · P.O. Box 3236, DSM
        </div>
    </div>

</body>
</html>
