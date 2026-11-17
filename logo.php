<?php
header("Content-type: image/svg+xml");
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 180 60" width="180" height="60">
  <defs>
    <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" style="stop-color:#4f46e5;stop-opacity:1" />
      <stop offset="100%" style="stop-color:#06b6d4;stop-opacity:1" />
    </linearGradient>
  </defs>
  <rect x="2" y="2" width="176" height="56" rx="12" ry="12" fill="url(#grad)" stroke="#1e293b" stroke-width="2"/>
  <text x="90" y="42" font-family="Arial Black, sans-serif" font-size="34" font-weight="900" 
        text-anchor="middle" fill="#ffffff" letter-spacing="2">ReNo</text>
  <circle cx="25" cy="30" r="10" fill="#fbbf24" stroke="#fff" stroke-width="2"/>
  <text x="25" y="36" font-family="Arial" font-size="14" font-weight="bold" text-anchor="middle" fill="#1e293b">A+</text>
</svg>