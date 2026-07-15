<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>No Products Found - PayCan</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 3rem;
            max-width: 600px;
            width: 100%;
            text-align: center;
        }

        .icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        h1 {
            font-size: 2rem;
            color: #1f2937;
            margin-bottom: 1rem;
        }

        p {
            color: #6b7280;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .instructions {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 1.5rem;
            margin: 2rem 0;
            text-align: left;
        }

        .instructions h2 {
            font-size: 1.125rem;
            color: #1f2937;
            margin-bottom: 1rem;
        }

        .instructions ol {
            margin-left: 1.5rem;
            color: #4b5563;
        }

        .instructions li {
            margin-bottom: 0.5rem;
        }

        .button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            padding: 0.875rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.2s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .button:active {
            transform: translateY(0);
        }

        .secondary-link {
            margin-top: 1rem;
            display: block;
            color: #6b7280;
            text-decoration: none;
        }

        .secondary-link:hover {
            color: #1f2937;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">📦</div>
        <h1>{{ $message }}</h1>
        <p>{{ $instructions }}</p>

        <div class="instructions">
            <h2>How to Create a Product:</h2>
            <ol>
                <li>Go to the Admin Panel</li>
                <li>Navigate to Products</li>
                <li>Click "Create Product"</li>
                <li>Add product details</li>
                <li>Add at least one price</li>
                <li>Set product as "Active"</li>
                <li>Return to this demo page</li>
            </ol>
        </div>

        <a href="{{ $adminUrl }}" class="button">Go to Admin Panel</a>
        <a href="/" class="secondary-link">← Back to Home</a>
    </div>
</body>
</html>
