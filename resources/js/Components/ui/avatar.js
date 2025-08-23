export const Avatar = {
  name: 'Avatar',
  template: `
    <div class="relative flex h-10 w-10 shrink-0 overflow-hidden rounded-full">
      <slot />
    </div>
  `
};

export const AvatarImage = {
  name: 'AvatarImage',
  props: {
    src: String,
    alt: String
  },
  template: `
    <img 
      v-if="src"
      :src="src" 
      :alt="alt"
      class="aspect-square h-full w-full object-cover"
    />
  `
};

export const AvatarFallback = {
  name: 'AvatarFallback',
  template: `
    <div class="flex h-full w-full items-center justify-center rounded-full bg-muted">
      <slot />
    </div>
  `
};
