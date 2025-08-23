export const ScrollArea = {
  name: 'ScrollArea',
  props: {
    orientation: {
      type: String,
      default: 'vertical'
    }
  },
  template: `
    <div class="relative overflow-auto" :class="orientation === 'horizontal' ? 'overflow-x-auto overflow-y-hidden' : 'overflow-y-auto overflow-x-hidden'">
      <slot />
    </div>
  `
};
