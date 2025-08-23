export const DropdownMenu = {
  name: 'DropdownMenu',
  data() {
    return {
      isOpen: false
    };
  },
  template: `
    <div class="relative inline-block text-left">
      <slot />
    </div>
  `,
  provide() {
    return {
      toggleDropdown: () => this.isOpen = !this.isOpen,
      closeDropdown: () => this.isOpen = false,
      isOpen: () => this.isOpen
    };
  }
};

export const DropdownMenuTrigger = {
  name: 'DropdownMenuTrigger',
  inject: ['toggleDropdown'],
  props: {
    asChild: Boolean
  },
  template: `
    <div @click="toggleDropdown">
      <slot />
    </div>
  `
};

export const DropdownMenuContent = {
  name: 'DropdownMenuContent',
  inject: ['isOpen', 'closeDropdown'],
  props: {
    align: {
      type: String,
      default: 'start'
    }
  },
  template: `
    <div 
      v-if="isOpen()"
      :class="contentClasses"
      @click.stop
    >
      <slot />
    </div>
  `,
  computed: {
    contentClasses() {
      const base = 'absolute z-50 min-w-[8rem] overflow-hidden rounded-md border bg-popover p-1 text-popover-foreground shadow-md data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2 bg-white border-gray-200 shadow-lg';
      const alignment = this.align === 'end' ? 'right-0' : 'left-0';
      return `${base} ${alignment} top-full mt-1`;
    }
  }
};

export const DropdownMenuItem = {
  name: 'DropdownMenuItem',
  inject: ['closeDropdown'],
  template: `
    <div 
      class="relative flex cursor-default select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none transition-colors focus:bg-accent focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 hover:bg-gray-100 cursor-pointer"
      @click="handleClick"
    >
      <slot />
    </div>
  `,
  methods: {
    handleClick(event) {
      this.$emit('click', event);
      this.closeDropdown();
    }
  }
};
