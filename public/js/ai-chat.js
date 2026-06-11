(function () {
  const widget = document.getElementById('ai-widget');
  if (!widget) return;

  const fab = document.getElementById('ai-fab');
  const panel = document.getElementById('ai-panel');
  const close = document.getElementById('ai-close');
  const form = document.getElementById('ai-form');
  const input = document.getElementById('ai-message');
  const imageInput = document.getElementById('ai-image-input');
  const attachments = document.getElementById('ai-attachments');
  const messages = document.getElementById('ai-messages');
  const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
  let mode = 'ask';
  let selectedImages = [];

  const modePrompts = {
    ask: 'Ask me anything about Pakistani recipes, cooking, ingredients, or food culture.',
    'meal-plan': 'Tell me your goal, diet, and preference. Example: Lose weight, non-veg, Pakistani food, mild spice.',
    ingredients: 'Type ingredients you have. Example: chicken, tomatoes, onion, garlic, yogurt.',
    nutrition: 'Ask a nutrition question. Example: Is biryani healthy? Best Pakistani food for weight loss?'
  };

  function storeGet(key) {
    try { return JSON.parse(localStorage.getItem('lazzat-' + key)) || null; } catch { return null; }
  }

  function appContext() {
    return {
      user: storeGet('user'),
      savedRecipes: storeGet('savedRecipes') || [],
      favorites: storeGet('favorites') || [],
      planner: storeGet('planner') || {},
      grocery: storeGet('grocery') || [],
      checkedGrocery: storeGet('grocery-checked') || [],
      language: localStorage.getItem('lazzat-lang') || 'en'
    };
  }

  function addMessage(text, type = 'bot') {
    const bubble = document.createElement('div');
    bubble.className = `ai-msg ${type}`;
    bubble.textContent = text;
    messages.appendChild(bubble);
    messages.scrollTop = messages.scrollHeight;
    return bubble;
  }

  function fileToImagePayload(file) {
    return new Promise((resolve, reject) => {
      const reader = new FileReader();
      reader.onload = () => {
        const result = String(reader.result || '');
        resolve({
          name: file.name,
          mimeType: file.type,
          data: result.split(',')[1] || '',
          preview: result
        });
      };
      reader.onerror = reject;
      reader.readAsDataURL(file);
    });
  }

  function renderAttachments() {
    attachments.innerHTML = selectedImages.map((image, index) => `
      <button class="ai-attachment" type="button" data-index="${index}" title="Remove image">
        <img src="${image.preview}" alt="${image.name}">
        <span>×</span>
      </button>
    `).join('');
    attachments.style.display = selectedImages.length ? 'flex' : 'none';
    attachments.querySelectorAll('.ai-attachment').forEach((button) => {
      button.addEventListener('click', () => {
        selectedImages.splice(Number(button.dataset.index), 1);
        renderAttachments();
      });
    });
  }

  function setLoading(isLoading) {
    form.classList.toggle('loading', isLoading);
    input.disabled = isLoading;
    form.querySelector('button').disabled = isLoading;
  }

  async function sendMessage(text) {
    const imageCount = selectedImages.length;
    const images = selectedImages.map(({ mimeType, data }) => ({ mimeType, data }));
    addMessage(imageCount ? `${text}\n[${imageCount} image${imageCount > 1 ? 's' : ''} attached]` : text, 'user');
    selectedImages = [];
    renderAttachments();
    setLoading(true);
    const typing = addMessage('Typing...', 'bot typing');

    try {
      const response = await fetch('/ai/chat', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrf
        },
        body: JSON.stringify({ message: text, mode, context: appContext(), images })
      });
      const data = await response.json();
      typing.classList.remove('typing');
      typing.textContent = data.reply || 'No response received.';
    } catch (error) {
      typing.classList.remove('typing');
      typing.textContent = 'AI is not available right now. Please check your internet connection or Gemini API key.';
    } finally {
      setLoading(false);
      input.focus();
      messages.scrollTop = messages.scrollHeight;
    }
  }

  fab.addEventListener('click', () => {
    widget.classList.toggle('open');
    input.focus();
  });

  close.addEventListener('click', () => widget.classList.remove('open'));

  document.querySelectorAll('.ai-actions button').forEach((button) => {
    button.addEventListener('click', () => {
      mode = button.dataset.mode;
      document.querySelectorAll('.ai-actions button').forEach(btn => btn.classList.toggle('active', btn === button));
      addMessage(modePrompts[mode], 'bot');
      input.placeholder = modePrompts[mode];
      input.focus();
    });
  });

  imageInput.addEventListener('change', async () => {
    const files = [...imageInput.files].filter(file => ['image/jpeg', 'image/png', 'image/webp'].includes(file.type)).slice(0, 3);
    selectedImages = await Promise.all(files.map(fileToImagePayload));
    imageInput.value = '';
    renderAttachments();
    if (selectedImages.length) {
      widget.classList.add('open');
      input.placeholder = 'Ask about this food image, ingredients, calories, or recipe...';
      input.focus();
    }
  });

  form.addEventListener('submit', (event) => {
    event.preventDefault();
    const text = input.value.trim();
    if (!text && !selectedImages.length) return;
    input.value = '';
    sendMessage(text || 'Please analyze this food image and suggest Pakistani recipes, ingredients, or nutrition advice.');
  });
})();
