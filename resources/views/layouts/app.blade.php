<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Lazzat | لذّت')</title>
  <meta name="description" content="Discover authentic Pakistani recipes, plan weekly meals and auto-generate your grocery list.">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
@yield('content')

<div class="ai-widget" id="ai-widget">
  <button class="ai-fab" id="ai-fab" type="button" aria-label="Open Lazzat AI">
    <span class="ai-fab-orb">✦</span>
    <span class="ai-fab-text">Lazzat AI</span>
  </button>
  <section class="ai-panel" id="ai-panel" aria-label="Lazzat AI chat">
    <header class="ai-panel-head">
      <div>
        <strong>Lazzat AI</strong>
        <span>Pakistani food assistant</span>
      </div>
      <button type="button" id="ai-close" aria-label="Close AI chat">×</button>
    </header>
    <div class="ai-actions">
      <button type="button" data-mode="ask">Ask Anything</button>
      <button type="button" data-mode="meal-plan">Meal Plan</button>
      <button type="button" data-mode="ingredients">My Ingredients</button>
      <button type="button" data-mode="nutrition">Nutrition Help</button>
    </div>
    <div class="ai-messages" id="ai-messages">
      <div class="ai-msg bot">Assalam-o-Alaikum! Ask me about Pakistani recipes, meal plans, ingredients, or nutrition. Urdu bhi chalegi.</div>
    </div>
    <div class="ai-attachments" id="ai-attachments"></div>
    <form class="ai-input" id="ai-form">
      <label class="ai-attach" title="Attach food image">
        📷
        <input id="ai-image-input" type="file" accept="image/png,image/jpeg,image/webp" multiple>
      </label>
      <input id="ai-message" type="text" placeholder="Ask about biryani, nihari, calories..." autocomplete="off">
      <button type="submit">Send</button>
    </form>
  </section>
</div>
<script src="{{ asset('js/ai-chat.js') }}"></script>
</body>
</html>
