<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Account Banned - Appeal</title>
    @vite('resources/css/style.css')
    <style>
        .banned-wrap {
            min-height: 100vh;
            width: 100%;
            background: #F0DED2;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .banned-card {
            width: min(760px, 100%);
            background: #FCEFE8;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.12);
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }
        .banned-card h1 {
            color: #23130B;
            font-size: 28px;
        }
        .banned-note {
            color: #6f4b3a;
            font-size: 15px;
        }
        .banned-comment {
            background: #fff7f2;
            border-radius: 8px;
            padding: 12px;
            border-left: 4px solid #a63333;
            color: #23130B;
        }
        .appeal-label {
            color: #23130B;
            font-weight: 600;
        }
        .appeal-textarea {
            width: 100%;
            min-height: 120px;
            resize: vertical;
            border: 1px solid #d9b9a8;
            border-radius: 8px;
            padding: 10px;
            color: #23130B;
            background: #fff;
            font-family: inherit;
        }
        .appeal-textarea:readonly {
            background: #f5f1ed;
            color: #6f4b3a;
            cursor: not-allowed;
            border-color: #c9b3a0;
        }
        .appeal-btn {
            border: none;
            border-radius: 8px;
            background: #E8B400;
            color: #23130B;
            font-weight: 600;
            padding: 10px 14px;
            cursor: pointer;
        }
        .appeal-meta {
            color: #6f4b3a;
            font-size: 13px;
        }
        .appeal-alert {
            border-radius: 8px;
            padding: 10px 12px;
            font-size: 14px;
        }
        .appeal-alert.success {
            background: #e8f6ed;
            color: #1f6b37;
        }
        .appeal-alert.error {
            background: #fdecec;
            color: #8d2929;
        }
    </style>
</head>
<body>
    <div class="banned-wrap">
        <div class="banned-card">
            <h1>Account Banned</h1>
            <p class="banned-note">Your account is currently banned due to the following comment:</p>
            <div class="banned-comment">
                {{ $bannedComment ?: 'No comment snapshot available.' }}
            </div>

            @if (session('success'))
                <div class="appeal-alert success">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="appeal-alert error">{{ $errors->first() }}</div>
            @endif

            @if (!$appealedAt)
                <form method="POST" action="{{ route('banned.appeal.submit') }}">
                    @csrf
                    <label class="appeal-label" for="appeal_message">Make an Appeal</label>
                    <textarea id="appeal_message" name="appeal_message" class="appeal-textarea" placeholder="Explain why your account should be reviewed..." required>{{ old('appeal_message', $appealMessage) }}</textarea>
                    <button type="submit" class="appeal-btn">Submit Appeal</button>
                </form>
            @else
                <div>
                    <label class="appeal-label" for="appeal_display">Your Appeal:</label>
                    <textarea id="appeal_display" class="appeal-textarea" readonly>{{ $appealMessage }}</textarea>
                    <p class="appeal-meta" style="margin-top: 10px;">✓ Appeal submitted on: {{ $appealedAt->format('M d, Y h:i A') }}</p>
                </div>
            @endif

            <form action="/logout" method="POST">
                @csrf
                <button type="submit" class="appeal-btn" style="background:#9C6D55;color:#fff;">Sign Out</button>
            </form>
        </div>
    </div>
</body>
</html>
