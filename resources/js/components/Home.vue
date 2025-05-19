<template>
  <div :class="['app-wrapper', theme]">
    <div class="theme-switcher">
      <label class="switch">
        <input type="checkbox" v-model="isDark" @change="toggleTheme" />
        <span class="slider"></span>
      </label>
      <span>{{ isDark ? 'Тёмная тема' : 'Светлая тема' }}</span>
    </div>

    <div class="calendar-tool">
      <h2>📅 Загрузка календаря</h2>
      <input type="file" @change="handleFileUpload" class="file-input" />
      <button @click="uploadFile" class="btn primary">Загрузить</button>

      <h2>📆 Расчет рабочих дней</h2>
      <div class="date-inputs">
        <input type="date" v-model="startDate" />
        <input type="date" v-model="endDate" />
      </div>
      <button @click="calculateDays" class="btn secondary">Рассчитать</button>

      <div v-if="result !== null" class="result">
        <strong>Рабочих дней: {{ result }}</strong>
      </div>
      <div v-if="showNotification" :class="['toast', notification.type]">
        {{ notification.message }}
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  data() {
    return {
      file: null,
      startDate: '',
      endDate: '',
      result: null,
      isDark: false,
      theme: 'light',
      notification: {
      message: '',
      type: '' // 'success' | 'error'
      },
      showNotification: false,
    };
  },
    methods: {
    showToast(message, type = 'success') {
    this.notification.message = message;
    this.notification.type = type;
    this.showNotification = true;
    setTimeout(() => {
    this.showNotification = false;
   }, 3000);
   },
    handleFileUpload(event) {
      this.file = event.target.files[0];
    },
    async uploadFile() {
    if (!this.file) {
    this.showToast('Выберите файл!', 'error');
    return;
    }

    const formData = new FormData();
    formData.append('file', this.file);

    try {
    await axios.post('/upload-calendar', formData);
    this.showToast('Файл успешно загружен!', 'success');
    } catch (error) {
    this.showToast('Ошибка при загрузке файла', 'error');
    }
    },
    async calculateDays() {
      const response = await axios.post('/calculate', {
        start_date: this.startDate,
        end_date: this.endDate
      });
      this.result = response.data.workdays;
    },
    toggleTheme() {
      this.theme = this.isDark ? 'dark' : 'light';
    }
  }
}
</script>

<style>
/* --- Общие стили и переменные для тем --- */
:root {
  /* Светлая тема */
  --bg-gradient-light: linear-gradient(135deg, #a1c4fd, #c2e9fb);
  --text-light: #222;
  --bg-panel-light: #f9f9ff;
  --border-light: #e0e0f0;
  --btn-primary-start-light: #4facfe;
  --btn-primary-end-light: #00f2fe;
  --btn-secondary-start-light: #43e97b;
  --btn-secondary-end-light: #38f9d7;
  --result-bg-light: #e8f5e9;
  --result-border-light: #66bb6a;

  /* Тёмная тема */
  --bg-gradient-dark: linear-gradient(135deg, #232526, #1c1c1c);
  --text-dark: #eee;
  --bg-panel-dark: #2a2a3a;
  --border-dark: #444466;
  --btn-primary-start-dark: #6a11cb;
  --btn-primary-end-dark: #2575fc;
  --btn-secondary-start-dark: #11998e;
  --btn-secondary-end-dark: #38ef7d;
  --result-bg-dark: #1b2a1b;
  --result-border-dark: #44aa44;
}

/* --- Обёртка всего приложения с фоном --- */
.app-wrapper {
  min-height: 100vh;
  padding: 20px;
  transition: background 0.5s ease;
  display: flex;
  flex-direction: column;
  align-items: center;
}

/* Светлая тема */
.app-wrapper.light {
  background: var(--bg-gradient-light);
  color: var(--text-light);
}

/* Тёмная тема */
.app-wrapper.dark {
  background: var(--bg-gradient-dark);
  color: var(--text-dark);
}

/* --- Компонент календаря --- */
.calendar-tool {
  max-width: 600px;
  width: 100%;
  background: var(--bg-panel-light);
  border: 1px solid var(--border-light);
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 8px 16px rgba(0,0,0,0.05);
  font-family: Arial, sans-serif;
  transition: background 0.5s ease, border-color 0.5s ease, color 0.5s ease;
}

/* Тёмная тема стили для панели */
.app-wrapper.dark .calendar-tool {
  background: var(--bg-panel-dark);
  border-color: var(--border-dark);
  color: var(--text-dark);
}

h2 {
  margin-bottom: 12px;
}

/* Инпуты */
.file-input,
input[type="date"] {
  display: block;
  margin-bottom: 12px;
  padding: 10px;
  font-size: 14px;
  border: 1px solid #ccc;
  border-radius: 8px;
  width: 100%;
  box-sizing: border-box;
  transition: background 0.3s ease, color 0.3s ease;
}

/* Тёмная тема инпуты */
.app-wrapper.dark .file-input,
.app-wrapper.dark input[type="date"] {
  background: #1f1f2a;
  border: 1px solid #555;
  color: var(--text-dark);
}

/* Группа дат в одну строку */
.date-inputs {
  display: flex;
  gap: 10px;
  margin-bottom: 12px;
}

/* Кнопки */
.btn {
  padding: 10px 18px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 14px;
  transition: background 0.3s ease, transform 0.2s ease;
  margin-right: 10px;
  color: white;
  user-select: none;
}

/* Светлая тема кнопок */
.btn.primary {
  background: linear-gradient(to right, var(--btn-primary-start-light), var(--btn-primary-end-light));
}
.btn.secondary {
  background: linear-gradient(to right, var(--btn-secondary-start-light), var(--btn-secondary-end-light));
}

/* Тёмная тема кнопок */
.app-wrapper.dark .btn.primary {
  background: linear-gradient(to right, var(--btn-primary-start-dark), var(--btn-primary-end-dark));
}
.app-wrapper.dark .btn.secondary {
  background: linear-gradient(to right, var(--btn-secondary-start-dark), var(--btn-secondary-end-dark));
}

.btn:hover {
  transform: translateY(-2px);
  opacity: 0.95;
}

/* Результат */
.result {
  margin-top: 20px;
  font-size: 18px;
  padding: 10px;
  background: var(--result-bg-light);
  border-left: 4px solid var(--result-border-light);
  border-radius: 6px;
  transition: background 0.5s ease, border-color 0.5s ease, color 0.5s ease;
}

/* Тёмная тема результата */
.app-wrapper.dark .result {
  background: var(--result-bg-dark);
  border-color: var(--result-border-dark);
  color: var(--text-dark);
}

/* --- Переключатель темы --- */
.theme-switcher {
  position: fixed;
  top: 20px;
  right: 20px;
  user-select: none;
  display: flex;
  align-items: center;
  gap: 10px;
  color: inherit;
  font-weight: 600;
  font-family: Arial, sans-serif;
}

/* Переключатель в стиле iOS */
.switch {
  position: relative;
  display: inline-block;
  width: 44px;
  height: 24px;
}

.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0; left: 0; right: 0; bottom: 0;
  background-color: #ccc;
  border-radius: 24px;
  transition: 0.4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 18px;
  width: 18px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  border-radius: 50%;
  transition: 0.4s;
}

input:checked + .slider {
  background-color: #4caf50;
}

input:checked + .slider:before {
  transform: translateX(20px);
}

.toast {
  margin-top: 20px;
  padding: 12px 16px;
  border-radius: 8px;
  font-weight: bold;
  text-align: center;
  transition: opacity 0.3s ease;
}

.toast.success {
  background-color: #d4edda;
  color: #155724;
  border-left: 6px solid #28a745;
}

.toast.error {
  background-color: #f8d7da;
  color: #721c24;
  border-left: 6px solid #dc3545;
}

.app-wrapper.dark .toast.success {
  background-color: #264d36;
  color: #c5f6c7;
  border-color: #28a745;
}

.app-wrapper.dark .toast.error {
  background-color: #5b2b2e;
  color: #ffcdd2;
  border-color: #dc3545;
}

</style>