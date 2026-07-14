<template>
  <svg 
    xmlns="http://www.w3.org/2000/svg" 
    :width="size" 
    :height="size" 
    viewBox="0 0 24 24" 
    fill="none" 
    stroke="currentColor" 
    stroke-width="2" 
    stroke-linecap="round" 
    stroke-linejoin="round"
    class="lucide-icon inline-block align-middle"
  >
    <path v-for="(d, idx) in paths" :key="'p'+idx" :d="d" />
    <circle v-for="(c, idx) in circles" :key="'c'+idx" :cx="c.cx" :cy="c.cy" :r="c.r" />
    <line v-for="(l, idx) in lines" :key="'l'+idx" :x1="l.x1" :y1="l.y1" :x2="l.x2" :y2="l.y2" />
    <rect v-for="(r, idx) in rects" :key="'r'+idx" :x="r.x" :y="r.y" :width="r.width" :height="r.height" :rx="r.rx" :ry="r.ry" />
    <polyline v-for="(p, idx) in polylines" :key="'pl'+idx" :points="p" />
  </svg>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  name: {
    type: String,
    required: true
  },
  size: {
    type: [Number, String],
    default: 20
  }
})

const iconData = {
  LayoutDashboard: {
    rects: [
      { x: 3, y: 3, width: 7, height: 9, rx: 1 },
      { x: 14, y: 3, width: 7, height: 5, rx: 1 },
      { x: 14, y: 12, width: 7, height: 9, rx: 1 },
      { x: 3, y: 16, width: 7, height: 5, rx: 1 }
    ]
  },
  Folder: {
    paths: [
      "M20 20a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.9a2 2 0 0 1-1.69-.9L9.6 3.9A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2Z"
    ]
  },
  CheckSquare: {
    paths: [
      "m9 11 3 3L22 4",
      "M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"
    ]
  },
  CreditCard: {
    rects: [
      { x: 2, y: 5, width: 20, height: 14, rx: 2 }
    ],
    lines: [
      { x1: 2, y1: 10, x2: 22, y2: 10 },
      { x1: 6, y1: 15, x2: 10, y2: 15 }
    ]
  },
  BarChart3: {
    paths: [
      "M3 3v18h18",
      "M18 17V9",
      "M12 17V5",
      "M6 17v-4"
    ]
  },
  Map: {
    paths: [
      "M3 6 9 3 15 6 21 3v15l-6 3-6-3-6 3V6zm6-3v15m6-12v15"
    ]
  },
  Settings: {
    paths: [
      "M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.1a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"
    ],
    circles: [
      { cx: 12, cy: 12, r: 3 }
    ]
  },
  LogOut: {
    paths: [
      "M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4",
      "m16 17 5-5-5-5",
      "M21 12H9"
    ]
  },
  User: {
    paths: [
      "M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"
    ],
    circles: [
      { cx: 12, cy: 7, r: 4 }
    ]
  },
  Shield: {
    paths: [
      "M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c4 0 7-2 7-2s3 2 7 2a1 1 0 0 1 1 1v7z"
    ]
  },
  Bell: {
    paths: [
      "M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9",
      "M10.3 21a1.94 1.94 0 0 0 3.4 0"
    ]
  },
  Users: {
    paths: [
      "M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2",
      "M22 21v-2a4 4 0 0 0-3-3.87",
      "M16 3.13a4 4 0 0 1 0 7.75"
    ],
    circles: [
      { cx: 9, cy: 7, r: 4 }
    ]
  },
  Settings2: {
    paths: [
      "M20 21v-2",
      "M20 15V3",
      "M12 21V11",
      "M12 7V3",
      "M4 21v-4",
      "M4 13V3"
    ],
    circles: [
      { cx: 4, cy: 15, r: 2 },
      { cx: 12, cy: 9, r: 2 },
      { cx: 20, cy: 17, r: 2 }
    ]
  },
  Camera: {
    paths: [
      "M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"
    ],
    circles: [
      { cx: 12, cy: 13, r: 3 }
    ]
  },
  Key: {
    paths: [
      "m21 2-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0 3 3L22 7l-3-3m-3.5 3.5L19 4"
    ]
  },
  Smartphone: {
    rects: [
      { x: 5, y: 2, width: 14, height: 20, rx: 2 }
    ],
    paths: [
      "M12 18h.01"
    ]
  },
  UserPlus: {
    paths: [
      "M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2",
      "M19 8v6",
      "M22 11h-6"
    ],
    circles: [
      { cx: 9, cy: 7, r: 4 }
    ]
  },
  Database: {
    paths: [
      "M12 8c4.418 0 8-1.79 8-4s-3.582-4-8-4-8 1.79-8 4 3.582 4 8 4z",
      "M4 12c0 2.21 3.582 4 8 4s8-1.79 8-4",
      "M4 20c0 2.21 3.582 4 8 4s8-1.79 8-4",
      "M4 4v16",
      "M20 4v16"
    ]
  },
  FileImage: {
    paths: [
      "M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7z",
      "M14 2v4a2 2 0 0 0 2 2h4"
    ],
    circles: [
      { cx: 9, cy: 9, r: 1 }
    ],
    polylines: [
      "10 18 13 14 17 18",
      "4 18 8 13 11 17"
    ]
  },
  AlertTriangle: {
    paths: [
      "m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z",
      "M12 9v4",
      "M12 17h.01"
    ]
  },
  Wallet: {
    paths: [
      "M19 7V4a1 1 0 0 0-1-1H5a2 2 0 0 0 0 4h15a1 1 0 0 1 1 1v4h-3a2 2 0 0 0 0 4h3a1 1 0 0 1-1 1v2a1 1 0 0 1-1 1H4a2 2 0 0 1-2-2V5"
    ]
  },
  TrendingUp: {
    polylines: [
      "22 7 13.5 15.5 8.5 10.5 2 17"
    ],
    paths: [
      "M16 7h6v6"
    ]
  },
  Landmark: {
    paths: [
      "m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z",
      "M12 22V9",
      "M8 22V9",
      "M16 22V9",
      "M20 9H4",
      "M20 22H4"
    ]
  },
  Clock: {
    circles: [
      { cx: 12, cy: 12, r: 10 }
    ],
    paths: [
      "M12 6v6l4 2"
    ]
  },
  Plus: {
    paths: [
      "M5 12h14",
      "M12 5v14"
    ]
  },
  Filter: {
    paths: [
      "M22 3H2l8 9.46V19l4 2v-8.54L22 3z"
    ]
  },
  Calendar: {
    rects: [
      { x: 3, y: 4, width: 18, height: 18, rx: 2 }
    ],
    lines: [
      { x1: 16, y1: 2, x2: 16, y2: 6 },
      { x1: 8, y1: 2, x2: 8, y2: 6 },
      { x1: 3, y1: 10, x2: 21, y2: 10 }
    ]
  }
}

const activeIcon = computed(() => {
  return iconData[props.name] || { paths: [] }
})

const paths = computed(() => activeIcon.value.paths || [])
const circles = computed(() => activeIcon.value.circles || [])
const lines = computed(() => activeIcon.value.lines || [])
const rects = computed(() => activeIcon.value.rects || [])
const polylines = computed(() => activeIcon.value.polylines || [])
</script>
