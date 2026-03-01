<?php
// Load models from JSON file
$modelsFile = 'models.json';
$models = file_exists($modelsFile) ? json_decode(file_get_contents($modelsFile), true) : [];

// If models file doesn't exist, create with default models
if (empty($models)) {
    $models = [
        ["id" => "nvidia/nemotron-3-nano-30b-a3b:free", "name" => "Nemotron-3 Nano 30B", "free" => true],
        ["id" => "nvidia/nemotron-nano-12b-v2-vl:free", "name" => "Nemotron Nano 12B V2-VL", "free" => true],
        ["id" => "deepseek/deepseek-r1", "name" => "DeepSeek-R1", "free" => false],
        ["id" => "meta-llama/llama-3-70b-instruct", "name" => "LLaMA-3 70B", "free" => false],
        ["id" => "meta-llama/llama-3.3-70b-instruct:free", "name" => "LLaMA-3.3 70B", "free" => true],
        ["id" => "mistralai/mixtral-8x7b-instruct", "name" => "Mixtral 8×7B", "free" => false],
        ["id" => "liquid/lfm-2.5-1.2b-instruct:free", "name" => "Liquid LFM-2.5 1.2B", "free" => true],
        ["id" => "google/gemma-3n-e2b-it:free", "name" => "Gemma-3N E2B-IT", "free" => true],
        ["id" => "google/gemma-3n-e4b-it:free", "name" => "Gemma-3N E4B-IT", "free" => true]
    ];
    file_put_contents($modelsFile, json_encode($models, JSON_PRETTY_PRINT));
}

// Encode models for JavaScript
$modelsJson = json_encode($models);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>STWAI – Multi-AI Chat</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  
  <!-- Fonts -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600&display=swap">

  <!-- Highlight.js -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/styles/github-dark.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/highlight.min.js"></script>
  
  <!-- Load all languages -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/languages/javascript.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/languages/python.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/languages/java.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/languages/c.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/languages/cpp.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/languages/csharp.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/languages/go.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/languages/rust.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/languages/sql.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/languages/bash.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/languages/json.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/languages/html.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/languages/css.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/languages/typescript.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/languages/php.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/languages/ruby.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/languages/swift.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/languages/kotlin.min.js"></script>

  <style>
    :root {
      --bg-primary: #0a0a0a;
      --bg-secondary: #151515;
      --bg-tertiary: #202020;
      --border-color: #333;
      --text-primary: #ffffff;
      --text-secondary: #a0a0a0;
      --accent-primary: #10a37f;
      --accent-hover: #0d8c6d;
      --accent-secondary: #6366f1;
      --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
      --code-bg: #1a1a1a;
      --inline-code-bg: #2a2a2a;
      --line-number-color: #6b7280;
      --cursor-color: #10a37f;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      background: var(--bg-primary);
      color: var(--text-primary);
      font-family: 'Inter', sans-serif;
      line-height: 1.6;
      min-height: 100vh;
    }

    .container {
      max-width: 1400px;
      margin: 0 auto;
      padding: 0 20px;
    }

    /* Header */
    .header {
      padding: 24px 0;
      border-bottom: 1px solid var(--border-color);
      background: rgba(10, 10, 10, 0.95);
      backdrop-filter: blur(10px);
      position: sticky;
      top: 0;
      z-index: 100;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .logo {
      font-size: 24px;
      font-weight: 700;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .logo span {
      background: linear-gradient(135deg, #10a37f, #6366f1);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .tagline {
      font-size: 14px;
      color: var(--text-secondary);
      margin-top: 6px;
    }

    .admin-link {
      background: var(--bg-tertiary);
      color: var(--text-primary);
      padding: 8px 16px;
      border-radius: 8px;
      text-decoration: none;
      font-size: 14px;
      border: 1px solid var(--border-color);
      transition: all 0.2s;
    }

    .admin-link:hover {
      border-color: var(--accent-primary);
      color: var(--accent-primary);
    }

    /* Main Layout */
    .main-layout {
      display: flex;
      flex-direction: column;
      gap: 28px;
      padding: 28px 0;
    }

    @media (min-width: 1024px) {
      .main-layout {
        flex-direction: row;
      }
    }

    /* Models Panel */
    .models-panel {
      flex: 0 0 auto;
    }

    @media (min-width: 1024px) {
      .models-panel {
        width: 340px;
      }
    }

    .panel-card {
      background: var(--bg-secondary);
      border-radius: 16px;
      padding: 24px;
      margin-bottom: 20px;
      border: 1px solid var(--border-color);
      box-shadow: var(--card-shadow);
    }

    .panel-title {
      font-size: 16px;
      font-weight: 600;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .models-grid {
      display: grid;
      gap: 12px;
      max-height: 400px;
      overflow-y: auto;
      padding-right: 8px;
    }

    .models-grid::-webkit-scrollbar {
      width: 6px;
    }

    .models-grid::-webkit-scrollbar-track {
      background: var(--bg-tertiary);
      border-radius: 3px;
    }

    .models-grid::-webkit-scrollbar-thumb {
      background: var(--border-color);
      border-radius: 3px;
    }

    .models-grid::-webkit-scrollbar-thumb:hover {
      background: var(--accent-primary);
    }

    .model-option {
      display: flex;
      align-items: center;
      padding: 14px;
      background: var(--bg-tertiary);
      border-radius: 10px;
      border: 1px solid var(--border-color);
      cursor: pointer;
      transition: all 0.2s;
    }

    .model-option:hover {
      border-color: var(--accent-primary);
      background: rgba(16, 163, 127, 0.1);
      transform: translateX(4px);
    }

    .model-option input[type="checkbox"] {
      margin-right: 14px;
      width: 18px;
      height: 18px;
      accent-color: var(--accent-primary);
    }

    .model-name {
      font-size: 14px;
      font-weight: 500;
    }

    .model-free {
      font-size: 11px;
      color: var(--accent-primary);
      margin-left: auto;
      background: rgba(16, 163, 127, 0.15);
      padding: 4px 10px;
      border-radius: 20px;
      font-weight: 600;
    }

    .counter {
      font-size: 13px;
      color: var(--text-secondary);
      margin-top: 20px;
      display: flex;
      justify-content: space-between;
      padding-top: 16px;
      border-top: 1px solid var(--border-color);
    }

    /* Chat Area */
    .chat-area {
      flex: 1;
      display: flex;
      flex-direction: column;
      min-height: 80vh;
      gap: 28px;
    }

    /* Responses Section */
    .responses-section {
      flex: 1;
      display: flex;
      flex-direction: column;
    }

    .responses-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }

    .responses-title {
      font-size: 18px;
      font-weight: 600;
      background: linear-gradient(135deg, #10a37f, #6366f1);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .layout-toggle {
      display: flex;
      background: var(--bg-tertiary);
      border-radius: 10px;
      padding: 4px;
      border: 1px solid var(--border-color);
      gap: 4px;
    }

    .layout-btn {
      padding: 8px 16px;
      border-radius: 8px;
      background: none;
      border: none;
      color: var(--text-secondary);
      font-size: 13px;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.2s;
    }

    .layout-btn.active {
      background: var(--accent-primary);
      color: white;
    }

    .responses-container {
      display: grid;
      gap: 24px;
      flex: 1;
      min-height: 500px;
    }

    /* Response Cards */
    .response-card {
      background: var(--bg-secondary);
      border-radius: 20px;
      padding: 28px;
      border: 1px solid var(--border-color);
      box-shadow: var(--card-shadow);
      display: flex;
      flex-direction: column;
      height: 100%;
      transition: all 0.3s;
      position: relative;
      overflow: hidden;
    }

    .response-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, var(--accent-primary), var(--accent-secondary));
      opacity: 0;
      transition: opacity 0.3s;
    }

    .response-card:hover::before {
      opacity: 1;
    }

    .response-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
    }

    .response-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 24px;
      padding-bottom: 16px;
      border-bottom: 1px solid var(--border-color);
    }

    .model-badge {
      display: flex;
      align-items: center;
      gap: 14px;
      font-weight: 600;
      font-size: 16px;
    }

    .model-icon {
      width: 36px;
      height: 36px;
      background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 16px;
      font-weight: bold;
      box-shadow: 0 4px 10px rgba(16, 163, 127, 0.3);
    }

    .response-content {
      flex: 1;
      overflow-y: auto;
      font-size: 15px;
      line-height: 1.8;
      color: var(--text-primary);
      padding-right: 4px;
    }

    /* Professional Code Formatting with Line Numbers */
    .response-content pre {
      position: relative;
      background: var(--code-bg);
      border-radius: 12px;
      margin: 20px 0;
      padding: 0;
      border: 1px solid var(--border-color);
      overflow: hidden;
    }

    .response-content pre::before {
      content: attr(data-language);
      position: absolute;
      top: 0;
      right: 0;
      background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
      color: white;
      padding: 6px 16px;
      font-size: 12px;
      font-weight: 600;
      border-radius: 0 12px 0 12px;
      text-transform: uppercase;
      z-index: 20;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }

    .code-block-wrapper {
      display: flex;
      background: var(--code-bg);
      counter-reset: line;
      max-height: 500px;
      overflow: auto;
    }

    .line-numbers {
      padding: 20px 0 20px 16px;
      text-align: right;
      background: var(--code-bg);
      border-right: 1px solid var(--border-color);
      user-select: none;
      min-width: 50px;
      font-family: 'Fira Code', monospace;
      font-size: 13px;
      line-height: 1.6;
      color: var(--line-number-color);
      white-space: pre;
    }

    .line-numbers span {
      counter-increment: line;
      display: block;
    }

    .line-numbers span::before {
      content: counter(line);
      display: block;
      color: var(--line-number-color);
      opacity: 0.7;
      font-size: 12px;
    }

    .code-content {
      flex: 1;
      padding: 20px;
      overflow-x: auto;
      background: var(--code-bg);
    }

    .code-content pre {
      margin: 0;
      padding: 0;
      border: none;
      background: transparent;
    }

    .code-content pre::before {
      display: none;
    }

    .code-content code {
      font-family: 'Fira Code', 'Consolas', monospace;
      font-size: 13px;
      line-height: 1.6;
      white-space: pre;
      display: block;
      background: transparent !important;
    }

    /* Inline code */
    .response-content code:not(pre code) {
      font-family: 'Fira Code', monospace;
      font-size: 13px;
      background: var(--inline-code-bg);
      padding: 2px 8px;
      border-radius: 6px;
      border: 1px solid var(--border-color);
      color: #e6e6e6;
    }

    /* Tables */
    .response-content table {
      width: 100%;
      border-collapse: collapse;
      margin: 16px 0;
      background: var(--bg-tertiary);
      border-radius: 12px;
      overflow: hidden;
      border: 1px solid var(--border-color);
    }

    .response-content thead {
      background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
    }

    .response-content th {
      color: white;
      font-weight: 600;
      padding: 14px 16px;
      text-align: left;
    }

    .response-content td {
      padding: 12px 16px;
      border-top: 1px solid var(--border-color);
    }

    .response-content tbody tr:nth-child(even) {
      background: rgba(255, 255, 255, 0.02);
    }

    .response-content tbody tr:hover {
      background: rgba(16, 163, 127, 0.08);
    }

    /* Writing Animation */
    .writing-container {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      min-height: 300px;
      gap: 24px;
      background: var(--bg-tertiary);
      border-radius: 16px;
      padding: 32px;
    }

    .typing-indicator {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .typing-dot {
      width: 14px;
      height: 14px;
      background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
      border-radius: 50%;
      animation: typingWave 1.4s infinite ease-in-out;
    }

    .typing-dot:nth-child(1) { animation-delay: -0.32s; }
    .typing-dot:nth-child(2) { animation-delay: -0.16s; }
    .typing-dot:nth-child(3) { animation-delay: 0s; }

    @keyframes typingWave {
      0%, 60%, 100% { 
        transform: translateY(0);
        opacity: 0.6;
      }
      30% { 
        transform: translateY(-16px);
        opacity: 1;
      }
    }

    /* Writing cursor animation */
    .writing-text {
      display: flex;
      align-items: center;
      gap: 8px;
      color: var(--text-secondary);
      font-size: 15px;
    }

    .cursor {
      width: 3px;
      height: 20px;
      background: var(--cursor-color);
      animation: blink 1s infinite;
      margin-left: 4px;
    }

    @keyframes blink {
      0%, 50% { opacity: 1; }
      51%, 100% { opacity: 0; }
    }

    /* Action Buttons */
    .action-buttons {
      display: flex;
      gap: 10px;
      align-items: center;
    }

    .action-button {
      background: var(--bg-tertiary);
      border: 1px solid var(--border-color);
      color: var(--text-secondary);
      border-radius: 10px;
      padding: 8px 16px;
      font-size: 13px;
      cursor: pointer;
      transition: all 0.2s;
      display: flex;
      align-items: center;
      gap: 8px;
      font-weight: 500;
    }

    .action-button:hover {
      background: var(--accent-primary);
      color: white;
      border-color: var(--accent-primary);
      transform: translateY(-1px);
    }

    .copy-button {
      background: rgba(16, 163, 127, 0.1);
      border: 1px solid rgba(16, 163, 127, 0.3);
      color: var(--accent-primary);
    }

    .copy-button:hover {
      background: var(--accent-primary);
      color: white;
    }

    /* Prompt Container */
    .prompt-container {
      background: var(--bg-secondary);
      border-radius: 20px;
      padding: 28px;
      border: 1px solid var(--border-color);
      box-shadow: var(--card-shadow);
    }

    .prompt-title {
      font-size: 16px;
      font-weight: 600;
      margin-bottom: 18px;
    }

    .prompt-textarea {
      width: 100%;
      min-height: 160px;
      padding: 20px;
      border-radius: 14px;
      border: 1px solid var(--border-color);
      background: var(--bg-tertiary);
      color: var(--text-primary);
      font-family: inherit;
      font-size: 15px;
      resize: vertical;
      transition: all 0.2s;
      line-height: 1.7;
    }

    .prompt-textarea:focus {
      outline: none;
      border-color: var(--accent-primary);
      box-shadow: 0 0 0 4px rgba(16, 163, 127, 0.15);
    }

    .send-button {
      margin-top: 22px;
      padding: 16px 32px;
      border-radius: 14px;
      border: none;
      cursor: pointer;
      background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
      color: white;
      font-weight: 600;
      font-size: 16px;
      display: flex;
      align-items: center;
      gap: 12px;
      transition: all 0.2s;
      width: 100%;
      justify-content: center;
    }

    .send-button:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(16, 163, 127, 0.3);
    }

    .send-button:disabled {
      opacity: 0.6;
      cursor: not-allowed;
      transform: none;
    }

    /* Footer */
    .footer {
      padding: 24px 0;
      text-align: center;
      color: var(--text-secondary);
      font-size: 13px;
      border-top: 1px solid var(--border-color);
      margin-top: 40px;
    }

    /* Grid Layout Classes */
    .grid-1 { grid-template-columns: 1fr; }
    .grid-2 { grid-template-columns: repeat(2, 1fr); }
    .grid-3 { grid-template-columns: repeat(3, 1fr); }
    .grid-4 { grid-template-columns: repeat(2, 1fr); }
    
    @media (min-width: 1200px) {
      .grid-4 { grid-template-columns: repeat(4, 1fr); }
    }

    /* Mobile */
    @media (max-width: 768px) {
      .container { padding: 0 16px; }
      .response-card { padding: 20px; }
      .responses-header { flex-direction: column; align-items: flex-start; gap: 16px; }
      .responses-container { grid-template-columns: 1fr !important; }
      
      .code-block-wrapper {
        flex-direction: column;
      }
      
      .line-numbers {
        border-right: none;
        border-bottom: 1px solid var(--border-color);
        padding: 12px 16px;
        min-width: 100%;
        text-align: left;
      }
      
      .line-numbers span {
        display: inline-block;
        margin-right: 12px;
      }
      
      .code-content {
        padding: 16px;
      }
    }

    /* Utilities */
    .hidden { display: none !important; }

    .status-message {
      padding: 16px 22px;
      border-radius: 12px;
      margin-bottom: 20px;
      font-size: 14px;
      animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
      from { opacity: 0; transform: translateY(-10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .status-error {
      background: rgba(239, 68, 68, 0.1);
      color: #ef4444;
      border: 1px solid rgba(239, 68, 68, 0.2);
    }

    .status-success {
      background: rgba(16, 163, 127, 0.1);
      color: var(--accent-primary);
      border: 1px solid rgba(16, 163, 127, 0.2);
    }
  </style>
</head>
<body>
  <div class="container">
    <header class="header">
      <div>
        <div class="logo">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="url(#gradient)" stroke-width="2"/>
            <path d="M2 17L12 22L22 17" stroke="url(#gradient)" stroke-width="2"/>
            <path d="M2 12L12 17L22 12" stroke="url(#gradient)" stroke-width="2"/>
            <defs>
              <linearGradient id="gradient" x1="2" y1="2" x2="22" y2="22">
                <stop offset="0%" stop-color="#10a37f"/>
                <stop offset="100%" stop-color="#6366f1"/>
              </linearGradient>
            </defs>
          </svg>
          STW<span>AI</span>
        </div>
        <p class="tagline">Multi-AI Chat with Professional Code Formatting & Line Numbers</p>
      </div>
      <a href="admin.php" class="admin-link">Admin Panel</a>
    </header>

    <div class="main-layout">
      <!-- Models Panel -->
      <div class="models-panel">
        <div class="panel-card">
          <h3 class="panel-title">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10a37f" stroke-width="2">
              <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z"/>
            </svg>
            Available Models
          </h3>
          
          <div class="models-grid" id="modelsContainer"></div>
          
          <div class="counter">
            <span>Selected: <span id="selectedCount">0</span> / 4</span>
            <span id="selectionStatus">Max 4 models</span>
          </div>
        </div>
        
        <div class="panel-card">
          <h3 class="panel-title">How to Use</h3>
          <p style="font-size: 14px; color: var(--text-secondary);">
            1. Select up to 4 models<br>
            2. Enter your prompt<br>
            3. Watch writing animation<br>
            4. Get formatted code with line numbers
          </p>
        </div>
      </div>
      
      <!-- Chat Area -->
      <div class="chat-area">
        <!-- Responses Section -->
        <div class="responses-section">
          <div class="responses-header">
            <h3 class="responses-title">AI Responses</h3>
            <div class="layout-toggle">
              <button class="layout-btn active" id="sideBySideBtn" onclick="setLayout('side-by-side')">Side-by-Side</button>
              <button class="layout-btn" id="stackedBtn" onclick="setLayout('stacked')">Stacked</button>
            </div>
          </div>
          
          <div id="statusMessage" class="hidden"></div>
          
          <div class="responses-container" id="responsesContainer">
            <div id="output"></div>
          </div>
        </div>
        
        <!-- Prompt Container -->
        <div class="prompt-container">
          <h3 class="prompt-title">Your Prompt</h3>
          <textarea class="prompt-textarea" id="prompt" placeholder="Ask for code, explanations, or comparisons...&#10;Example: 'Write a Python function to calculate fibonacci with memoization'"></textarea>
          <button class="send-button" id="sendButton" onclick="sendPrompt()">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
              <path d="M22 2L11 13M22 2L15 22L11 13M22 2L2 9L11 13"/>
            </svg>
            Send Prompt
          </button>
        </div>
      </div>
    </div>
    
    <footer class="footer">
      <p>STWAI – Professional Code Formatting with Line Numbers & Writing Animation</p>
    </footer>
  </div>

  <script>
    const API_URL = "api-handler.php";
    const models = <?php echo $modelsJson; ?>;
    
    let selectedModels = [];
    let currentLayout = 'side-by-side';
    
    function init() {
      renderModels();
      updateSelectedCount();
      setupEventListeners();
      updateResponsesGrid();
    }
    
    function renderModels() {
      const container = document.getElementById('modelsContainer');
      container.innerHTML = '';
      
      models.forEach(model => {
        const option = document.createElement('div');
        option.className = 'model-option';
        option.innerHTML = `
          <input type="checkbox" value="${model.id}" id="${model.id}">
          <label for="${model.id}" class="model-name">${model.name}</label>
          ${model.free ? '<span class="model-free">FREE</span>' : ''}
        `;
        container.appendChild(option);
      });
      
      document.querySelectorAll('.model-option input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', handleModelSelection);
      });
    }
    
    function handleModelSelection() {
      const checkedBoxes = document.querySelectorAll('.model-option input[type="checkbox"]:checked');
      selectedModels = Array.from(checkedBoxes).map(cb => cb.value);
      
      if (selectedModels.length > 4) {
        this.checked = false;
        selectedModels = selectedModels.filter(id => id !== this.value);
        showStatus('You can select maximum 4 models', 'error');
      }
      
      updateSelectedCount();
      updateResponsesGrid();
    }
    
    function updateSelectedCount() {
      document.getElementById('selectedCount').textContent = selectedModels.length;
    }
    
    function updateResponsesGrid() {
      const container = document.getElementById('responsesContainer');
      const count = Math.max(selectedModels.length, 1);
      
      container.innerHTML = '';
      container.className = 'responses-container';
      
      if (currentLayout === 'stacked') {
        container.classList.add('grid-1');
      } else {
        if (window.innerWidth <= 768) container.classList.add('grid-1');
        else if (count === 1) container.classList.add('grid-1');
        else if (count === 2) container.classList.add('grid-2');
        else if (count === 3) container.classList.add('grid-3');
        else container.classList.add('grid-4');
      }
      
      if (selectedModels.length > 0) {
        selectedModels.forEach(modelId => {
          const modelInfo = models.find(m => m.id === modelId);
          if (modelInfo) {
            container.appendChild(createEmptyResponseCard(modelInfo));
          }
        });
      } else {
        container.appendChild(createEmptyStateCard());
      }
    }
    
    function createEmptyResponseCard(modelInfo) {
      const card = document.createElement('div');
      card.className = 'response-card';
      card.id = `card-${modelInfo.id}`;
      card.innerHTML = `
        <div class="response-header">
          <div class="model-badge">
            <div class="model-icon">${modelInfo.name.charAt(0)}</div>
            <span>${modelInfo.name}</span>
          </div>
          <div class="action-buttons">
            ${modelInfo.free ? '<span class="model-free">FREE</span>' : ''}
            <button class="action-button copy-button" onclick="copyResponse('${modelInfo.id}')">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M8 16H6C4.89543 16 4 15.1046 4 14V6C4 4.89543 4.89543 4 6 4H14C15.1046 4 16 4.89543 16 6V8M10 20H18C19.1046 20 20 19.1046 20 18V10C20 8.89543 19.1046 8 18 8H10C8.89543 8 8 8.89543 8 10V18C8 19.1046 8.89543 20 10 20Z"/>
              </svg>
              Copy
            </button>
          </div>
        </div>
        <div class="response-content" id="content-${modelInfo.id}">
          <div class="writing-container">
            <div class="typing-indicator">
              <div class="typing-dot"></div>
              <div class="typing-dot"></div>
              <div class="typing-dot"></div>
            </div>
            <div class="writing-text">
              <span>AI is writing</span>
              <span class="cursor"></span>
            </div>
          </div>
        </div>
      `;
      return card;
    }
    
    function createEmptyStateCard() {
      const card = document.createElement('div');
      card.className = 'response-card';
      card.innerHTML = `
        <div class="response-header">
          <div class="model-badge">
            <div class="model-icon">🤖</div>
            <span>No Model Selected</span>
          </div>
        </div>
        <div class="response-content">
          <div style="padding: 40px; text-align: center; color: var(--text-secondary);">
            Select AI models from the left panel to see their responses here
          </div>
        </div>
      `;
      return card;
    }
    
    function setLayout(layout) {
      currentLayout = layout;
      document.getElementById('sideBySideBtn').classList.toggle('active', layout === 'side-by-side');
      document.getElementById('stackedBtn').classList.toggle('active', layout === 'stacked');
      updateResponsesGrid();
    }
    
    function showStatus(message, type) {
      const statusElement = document.getElementById('statusMessage');
      statusElement.textContent = message;
      statusElement.className = `status-message status-${type}`;
      statusElement.classList.remove('hidden');
      
      if (type === 'success') {
        setTimeout(() => statusElement.classList.add('hidden'), 5000);
      }
    }
    
    // Format code with line numbers
    function formatCodeWithLineNumbers(code, language) {
      const lines = code.split('\n');
      const lineNumbers = lines.map((_, i) => `<span></span>`).join('');
      
      return `
        <div class="code-block-wrapper">
          <div class="line-numbers">
            ${lineNumbers}
          </div>
          <div class="code-content">
            <pre data-language="${language}"><code class="language-${language}">${escapeHtml(code)}</code></pre>
          </div>
        </div>
      `;
    }
    
    // Enhanced formatter with line numbers for code blocks
    function formatResponse(text) {
      if (!text) return '<p>No response received</p>';
      
      let formatted = text;
      
      // Handle code blocks with line numbers
      formatted = formatted.replace(/```(\w+)?\n([\s\S]*?)```/g, (match, lang, code) => {
        const language = lang || detectLanguage(code);
        return formatCodeWithLineNumbers(code.trim(), language);
      });
      
      // Handle inline code
      formatted = formatted.replace(/`([^`]+)`/g, '<code>$1</code>');
      
      // Handle tables
      formatted = formatted.replace(/\|([^\n]+)\|\n\|([-:| ]+)+\|\n((?:\|[^\n]+\|\n?)+)/g, (match, headers, _, rows) => {
        const headerCells = headers.split('|').filter(c => c.trim()).map(c => c.trim());
        const rowLines = rows.trim().split('\n').filter(r => r.trim());
        
        let tableHtml = '<table><thead><tr>';
        headerCells.forEach(h => tableHtml += `<th>${h}</th>`);
        tableHtml += '</tr></thead><tbody>';
        
        rowLines.forEach(line => {
          const cells = line.split('|').filter(c => c.trim()).map(c => c.trim());
          if (cells.length === headerCells.length) {
            tableHtml += '<tr>';
            cells.forEach(c => tableHtml += `<td>${c}</td>`);
            tableHtml += '</tr>';
          }
        });
        
        tableHtml += '</tbody></table>';
        return tableHtml;
      });
      
      // Handle headers
      formatted = formatted.replace(/^### (.*$)/gm, '<h3>$1</h3>');
      formatted = formatted.replace(/^## (.*$)/gm, '<h2>$1</h2>');
      formatted = formatted.replace(/^# (.*$)/gm, '<h1>$1</h1>');
      
      // Handle lists
      formatted = formatted.replace(/^\s*[-*+]\s+(.+)/gm, '<li>$1</li>');
      formatted = formatted.replace(/(<li>.*<\/li>\n?)+/g, '<ul>$&</ul>');
      
      formatted = formatted.replace(/^\s*\d+\.\s+(.+)/gm, '<li>$1</li>');
      formatted = formatted.replace(/(<li>.*<\/li>\n?)+/g, '<ol>$&</ol>');
      
      // Handle paragraphs
      const parts = formatted.split(/\n\n+/);
      if (parts.length > 1 && !formatted.startsWith('<')) {
        formatted = parts.map(p => {
          if (!p.startsWith('<')) return `<p>${p}</p>`;
          return p;
        }).join('\n');
      } else if (!formatted.startsWith('<')) {
        formatted = `<p>${formatted}</p>`;
      }
      
      return formatted;
    }
    
    function detectLanguage(code) {
      const codeStr = code.toLowerCase();
      if (codeStr.includes('function') || codeStr.includes('=>') || codeStr.includes('const ')) return 'javascript';
      if (codeStr.includes('def ') || codeStr.includes('import ') || codeStr.includes('print(')) return 'python';
      if (codeStr.includes('public class') || codeStr.includes('System.out')) return 'java';
      if (codeStr.includes('#include') || codeStr.includes('int main')) return 'c';
      if (codeStr.includes('<!DOCTYPE') || codeStr.includes('<html')) return 'html';
      if (codeStr.includes('{') && codeStr.includes('}') && codeStr.includes(':')) return 'css';
      return 'plaintext';
    }
    
    function escapeHtml(text) {
      const div = document.createElement('div');
      div.textContent = text;
      return div.innerHTML;
    }
    
    function copyResponse(modelId) {
      const contentDiv = document.getElementById(`content-${modelId}`);
      if (!contentDiv) return;
      
      const text = contentDiv.textContent || contentDiv.innerText;
      
      navigator.clipboard.writeText(text).then(() => {
        showStatus('Response copied to clipboard!', 'success');
      }).catch(() => {
        showStatus('Failed to copy response', 'error');
      });
    }
    
    async function sendPrompt() {
      const prompt = document.getElementById('prompt').value.trim();
      const sendButton = document.getElementById('sendButton');
      
      if (!prompt) {
        showStatus('Please enter a prompt', 'error');
        return;
      }
      
      if (selectedModels.length === 0) {
        showStatus('Select at least one model', 'error');
        return;
      }
      
      sendButton.disabled = true;
      sendButton.innerHTML = `
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" style="animation: spin 1s linear infinite;">
          <path d="M12 2V6M12 18V22M6 12H2M22 12H18M19.07 4.93L16.24 7.76M7.76 16.24L4.93 19.07M19.07 19.07L16.24 16.24M7.76 7.76L4.93 4.93"/>
        </svg>
        Processing...
      `;
      
      selectedModels.forEach(modelId => {
        const contentDiv = document.getElementById(`content-${modelId}`);
        if (contentDiv) {
          contentDiv.innerHTML = `
            <div class="writing-container">
              <div class="typing-indicator">
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
              </div>
              <div class="writing-text">
                <span>Writing response</span>
                <span class="cursor"></span>
              </div>
            </div>
          `;
        }
      });
      
      document.getElementById('statusMessage').classList.add('hidden');
      
      try {
        const response = await fetch(API_URL, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ prompt, models: selectedModels })
        });
        
        if (!response.ok) throw new Error(`Server error: ${response.status}`);
        
        const data = await response.json();
        
        if (data.responses && Object.keys(data.responses).length > 0) {
          for (const modelId in data.responses) {
            const contentDiv = document.getElementById(`content-${modelId}`);
            if (contentDiv) {
              contentDiv.innerHTML = formatResponse(data.responses[modelId] || 'No response received');
            }
          }
          
          setTimeout(() => {
            document.querySelectorAll('pre code').forEach(block => {
              hljs.highlightElement(block);
            });
          }, 50);
          
          showStatus(`Received responses from ${Object.keys(data.responses).length} model(s)`, 'success');
        }
      } catch (error) {
        selectedModels.forEach(modelId => {
          const contentDiv = document.getElementById(`content-${modelId}`);
          if (contentDiv) {
            contentDiv.innerHTML = `<p style="color: #ef4444;">Error: ${error.message}</p>`;
          }
        });
        showStatus(`Error: ${error.message}`, 'error');
      } finally {
        sendButton.disabled = false;
        sendButton.innerHTML = `
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
            <path d="M22 2L11 13M22 2L15 22L11 13M22 2L2 9L11 13"/>
          </svg>
          Send Prompt
        `;
      }
    }
    
    function setupEventListeners() {
      document.getElementById('prompt').addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
          sendPrompt();
        }
      });
      
      window.addEventListener('resize', updateResponsesGrid);
    }
    
    document.addEventListener('DOMContentLoaded', init);
  </script>
</body>
</html>
