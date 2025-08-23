export const Button = {
  name: 'Button',
  props: {
    variant: {
      type: String,
      default: 'default'
    },
    size: {
      type: String, 
      default: 'default'
    },
    disabled: {
      type: Boolean,
      default: false
    },
    type: {
      type: String,
      default: 'button'
    }
  },
  template: `
    <button 
      :type="type"
      :disabled="disabled"
      :class="buttonClasses"
      @click="$emit('click', $event)"
    >
      <slot />
    </button>
  `,
  computed: {
    buttonClasses() {
      const base = 'inline-flex items-center justify-center rounded-md font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 disabled:pointer-events-none disabled:opacity-50';
      
      const variants = {
        default: 'bg-primary text-primary-foreground hover:bg-primary/90 bg-blue-600 text-white hover:bg-blue-700',
        ghost: 'hover:bg-accent hover:text-accent-foreground hover:bg-gray-100',
        outline: 'border border-input bg-background hover:bg-accent hover:text-accent-foreground border-gray-300 hover:bg-gray-50',
        secondary: 'bg-secondary text-secondary-foreground hover:bg-secondary/80 bg-gray-200 hover:bg-gray-300'
      };
      
      const sizes = {
        default: 'h-10 px-4 py-2',
        sm: 'h-9 rounded-md px-3',
        icon: 'h-10 w-10'
      };
      
      return `${base} ${variants[this.variant] || variants.default} ${sizes[this.size] || sizes.default}`;
    }
  }
};
