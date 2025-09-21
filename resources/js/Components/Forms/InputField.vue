<template>
  <div class="space-y-1">
    <label
      v-if="label"
      :for="id"
      class="block text-sm font-medium text-gray-700"
      :class="{ 'text-red-700': hasError }"
    >
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>

    <div class="relative">
      <!-- Input with icon -->
      <div v-if="icon" class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
        <component
          :is="icon"
          class="h-5 w-5 text-gray-400"
          :class="{ 'text-red-400': hasError }"
        />
      </div>

      <input
        :id="id"
        v-model="inputValue"
        :type="type"
        :placeholder="placeholder"
        :disabled="disabled"
        :readonly="readonly"
        :required="required"
        :min="min"
        :max="max"
        :step="step"
        :autocomplete="autocomplete"
        :class="[
          'block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm',
          {
            'pl-10': icon,
            'pr-10': showPasswordToggle || clearable,
            'border-red-300 focus:border-red-500 focus:ring-red-500': hasError,
            'bg-gray-50 text-gray-500 cursor-not-allowed': disabled,
            'bg-gray-50': readonly
          }
        ]"
        @blur="handleBlur"
        @focus="handleFocus"
        @input="handleInput"
      />

      <!-- Clear button -->
      <button
        v-if="clearable && inputValue && !disabled && !readonly"
        type="button"
        @click="clearValue"
        class="absolute inset-y-0 right-0 pr-3 flex items-center"
      >
        <svg class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>

      <!-- Password toggle -->
      <button
        v-if="showPasswordToggle && !disabled && !readonly"
        type="button"
        @click="togglePasswordVisibility"
        class="absolute inset-y-0 right-0 pr-3 flex items-center"
      >
        <svg v-if="showPassword" class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
        </svg>
        <svg v-else class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
        </svg>
      </button>
    </div>

    <!-- Help text -->
    <p v-if="helpText && !hasError" class="text-sm text-gray-500">
      {{ helpText }}
    </p>

    <!-- Error message -->
    <p v-if="hasError" class="text-sm text-red-600">
      {{ errorMessage }}
    </p>

    <!-- Character count -->
    <div v-if="showCharCount" class="flex justify-between text-xs text-gray-500">
      <span>{{ inputValue?.length || 0 }} characters</span>
      <span v-if="maxLength">of {{ maxLength }}</span>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  type: {
    type: String,
    default: 'text'
  },
  label: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: ''
  },
  helpText: {
    type: String,
    default: ''
  },
  errorMessage: {
    type: String,
    default: ''
  },
  required: {
    type: Boolean,
    default: false
  },
  disabled: {
    type: Boolean,
    default: false
  },
  readonly: {
    type: Boolean,
    default: false
  },
  clearable: {
    type: Boolean,
    default: false
  },
  showCharCount: {
    type: Boolean,
    default: false
  },
  maxLength: {
    type: Number,
    default: null
  },
  icon: {
    type: [String, Object],
    default: null
  },
  id: {
    type: String,
    default: () => `input-${Math.random().toString(36).substr(2, 9)}`
  },
  autocomplete: {
    type: String,
    default: 'off'
  },
  min: {
    type: Number,
    default: null
  },
  max: {
    type: Number,
    default: null
  },
  step: {
    type: Number,
    default: null
  },
  validate: {
    type: Function,
    default: null
  }
});

const emit = defineEmits(['update:modelValue', 'blur', 'focus', 'input', 'validate']);

const inputValue = ref(props.modelValue);
const showPassword = ref(false);
const hasError = ref(false);
const validationError = ref('');

const showPasswordToggle = computed(() => {
  return props.type === 'password';
});

const currentType = computed(() => {
  if (props.type === 'password' && showPassword.value) {
    return 'text';
  }
  return props.type;
});

const handleInput = (event) => {
  let value = event.target.value;
  
  // Handle number inputs
  if (props.type === 'number') {
    value = value === '' ? '' : Number(value);
  }
  
  inputValue.value = value;
  emit('update:modelValue', value);
  emit('input', value);
  
  // Validate on input if validation function is provided
  if (props.validate) {
    validateInput(value);
  }
};

const handleBlur = (event) => {
  emit('blur', event);
  
  // Validate on blur
  if (props.validate) {
    validateInput(inputValue.value);
  }
};

const handleFocus = (event) => {
  emit('focus', event);
};

const clearValue = () => {
  inputValue.value = '';
  emit('update:modelValue', '');
  emit('input', '');
};

const togglePasswordVisibility = () => {
  showPassword.value = !showPassword.value;
};

const validateInput = (value) => {
  if (!props.validate) return;
  
  try {
    const result = props.validate(value);
    if (typeof result === 'string') {
      validationError.value = result;
      hasError.value = true;
    } else if (result === false) {
      validationError.value = 'Invalid input';
      hasError.value = true;
    } else {
      validationError.value = '';
      hasError.value = false;
    }
  } catch (error) {
    validationError.value = error.message || 'Validation error';
    hasError.value = true;
  }
  
  emit('validate', {
    isValid: !hasError.value,
    error: validationError.value
  });
};

// Watch for external modelValue changes
watch(() => props.modelValue, (newValue) => {
  inputValue.value = newValue;
});

// Watch for external error changes
watch(() => props.errorMessage, (newError) => {
  hasError.value = !!newError;
});
</script>
</template>