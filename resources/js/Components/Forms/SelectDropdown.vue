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
      <!-- Select with icon -->
      <div v-if="icon" class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
        <component
          :is="icon"
          class="h-5 w-5 text-gray-400"
          :class="{ 'text-red-400': hasError }"
        />
      </div>

      <select
        :id="id"
        v-model="selectedValue"
        :disabled="disabled"
        :required="required"
        :class="[
          'block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm',
          {
            'pl-10': icon,
            'pr-10': clearable && selectedValue,
            'border-red-300 focus:border-red-500 focus:ring-red-500': hasError,
            'bg-gray-50 text-gray-500 cursor-not-allowed': disabled
          }
        ]"
        @change="handleChange"
        @blur="handleBlur"
        @focus="handleFocus"
      >
        <option v-if="placeholder" value="" disabled>
          {{ placeholder }}
        </option>
        <option
          v-for="option in options"
          :key="getOptionValue(option)"
          :value="getOptionValue(option)"
        >
          {{ getOptionLabel(option) }}
        </option>
      </select>

      <!-- Clear button -->
      <button
        v-if="clearable && selectedValue && !disabled"
        type="button"
        @click="clearValue"
        class="absolute inset-y-0 right-0 pr-3 flex items-center"
      >
        <svg class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
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

    <!-- Selected option info -->
    <div v-if="selectedOption && showSelectedInfo" class="mt-2 p-2 bg-green-50 rounded-md">
      <p class="text-sm text-green-800">
        Selected: {{ getOptionLabel(selectedOption) }}
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
  modelValue: {
    type: [String, Number, Object],
    default: null
  },
  options: {
    type: Array,
    required: true
  },
  label: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: 'Select an option'
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
  clearable: {
    type: Boolean,
    default: false
  },
  showSelectedInfo: {
    type: Boolean,
    default: false
  },
  icon: {
    type: [String, Object],
    default: null
  },
  id: {
    type: String,
    default: () => `select-${Math.random().toString(36).substr(2, 9)}`
  },
  valueKey: {
    type: String,
    default: 'value'
  },
  labelKey: {
    type: String,
    default: 'label'
  },
  validate: {
    type: Function,
    default: null
  }
});

const emit = defineEmits(['update:modelValue', 'change', 'blur', 'focus', 'validate']);

const selectedValue = ref(props.modelValue);
const hasError = ref(false);
const validationError = ref('');

const selectedOption = computed(() => {
  if (!selectedValue.value) return null;
  return props.options.find(option => getOptionValue(option) === selectedValue.value);
});

const getOptionValue = (option) => {
  if (typeof option === 'string' || typeof option === 'number') {
    return option;
  }
  return option[props.valueKey];
};

const getOptionLabel = (option) => {
  if (typeof option === 'string' || typeof option === 'number') {
    return option;
  }
  return option[props.labelKey];
};

const handleChange = (event) => {
  const value = event.target.value;
  selectedValue.value = value;
  emit('update:modelValue', value);
  emit('change', value);
  
  // Validate on change if validation function is provided
  if (props.validate) {
    validateInput(value);
  }
};

const handleBlur = (event) => {
  emit('blur', event);
  
  // Validate on blur
  if (props.validate) {
    validateInput(selectedValue.value);
  }
};

const handleFocus = (event) => {
  emit('focus', event);
};

const clearValue = () => {
  selectedValue.value = null;
  emit('update:modelValue', null);
  emit('change', null);
};

const validateInput = (value) => {
  if (!props.validate) return;
  
  try {
    const result = props.validate(value);
    if (typeof result === 'string') {
      validationError.value = result;
      hasError.value = true;
    } else if (result === false) {
      validationError.value = 'Invalid selection';
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
  selectedValue.value = newValue;
});

// Watch for external error changes
watch(() => props.errorMessage, (newError) => {
  hasError.value = !!newError;
});
</script>
</template>