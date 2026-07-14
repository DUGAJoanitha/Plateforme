import type { Config } from 'tailwindcss'

export default {
  darkMode: 'class',
  content: [
    './components/**/*.{js,vue,ts}',
    './layouts/**/*.vue',
    './pages/**/*.vue',
    './plugins/**/*.{js,ts}',
    './app.vue',
  ],
  theme: {
    extend: {
      colors: {
        /* ── Brand Palette ─────────────────────────────────── */
        'brand': {
          'dark':    '#083C44',   // Sidebar, headers, dark surfaces
          'mid':     '#22C7D6',   // Interactive, active states, glow
          'light':   '#8EF2FC',   // Accent, highlights, gradient end
          'surface': 'rgba(142,242,252,0.08)', // Subtle tinted surface
        },
        /* ── Design System Surfaces ─────────────────────────── */
        'ds': {
          'bg':           '#F8F9FF',
          'card':         '#FFFFFF',
          'border':       '#D9E1E7',
          'border-focus': '#22C7D6',
          'text-1':       '#121C2A',
          'text-2':       '#3E494A',
          'text-muted':   '#6E797A',
        },
        /* ── Semantic (status only) ─────────────────────────── */
        'state': {
          'success':  '#16A34A',
          'warning':  '#D97706',
          'error':    '#DC2626',
          'info':     '#22C7D6',
        }
      },
      fontFamily: {
        'display': ['Hanken Grotesk', 'sans-serif'],
        'sans':    ['Inter', 'sans-serif'],
        'mono':    ['JetBrains Mono', 'monospace'],
      },
      fontSize: {
        'display-lg': ['48px', { lineHeight: '56px', letterSpacing: '-0.02em', fontWeight: '700' }],
        'headline-lg': ['32px', { lineHeight: '40px', letterSpacing: '-0.01em', fontWeight: '600' }],
        'headline-md': ['24px', { lineHeight: '32px', fontWeight: '600' }],
        'title-lg':    ['20px', { lineHeight: '28px', fontWeight: '600' }],
        'body-lg':     ['18px', { lineHeight: '28px', fontWeight: '400' }],
        'body-md':     ['16px', { lineHeight: '24px', fontWeight: '400' }],
        'body-sm':     ['14px', { lineHeight: '20px', fontWeight: '400' }],
        'label-md':    ['13px', { lineHeight: '16px', letterSpacing: '0.02em', fontWeight: '500' }],
        'label-sm':    ['11px', { lineHeight: '14px', letterSpacing: '0.05em', fontWeight: '500' }],
      },
      borderRadius: {
        'DEFAULT': '0.25rem',
        'sm':      '0.125rem',
        'md':      '0.375rem',
        'lg':      '0.5rem',
        'xl':      '0.75rem',
        '2xl':     '1rem',
        '3xl':     '1.5rem',
        'full':    '9999px',
      },
      spacing: {
        'xs':  '4px',
        'sm':  '8px',
        'md':  '16px',
        'lg':  '24px',
        'xl':  '32px',
        '2xl': '48px',
        '3xl': '64px',
      },
      boxShadow: {
        'glow-sm':    '0 0 8px rgba(34,199,214,0.35)',
        'glow':       '0 0 16px rgba(34,199,214,0.5)',
        'glow-lg':    '0 0 28px rgba(34,199,214,0.6)',
        'card':       '0 4px 20px rgba(8,60,68,0.05)',
        'card-hover': '0 4px 24px rgba(8,60,68,0.1)',
        'sidebar':    '4px 0 32px rgba(8,60,68,0.3)',
      },
      backgroundImage: {
        'brand-gradient':     'linear-gradient(135deg, #22C7D6 0%, #8EF2FC 100%)',
        'brand-gradient-v':   'linear-gradient(180deg, #083C44 0%, #0a4d57 100%)',
        'brand-gradient-bar': 'linear-gradient(90deg, #22C7D6 0%, #8EF2FC 100%)',
      },
    }
  }
} satisfies Config
