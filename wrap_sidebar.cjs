const fs = require('fs');
const path = require('path');

const file = path.join(__dirname, 'resources/js/Components/Sidebar.vue');
let content = fs.readFileSync(file, 'utf8');

// The text is always on a new line after the closing </svg> tag, before the closing </Link> tag.
// e.g.
// </svg>
// Dashboard
// </Link>

content = content.replace(/(<\/svg>\s*)([\w\s\/&]+)(\s*<\/Link>)/g, (match, p1, p2, p3) => {
    return p1 + '<span class="nav-text">' + p2.trim() + '</span>' + p3;
});

// For the 'Pilih Modul' a tag
content = content.replace(/(<\/svg>\s*)(Pilih Modul)(\s*<\/a>)/g, (match, p1, p2, p3) => {
    return p1 + '<span class="nav-text">' + p2 + '</span>' + p3;
});

fs.writeFileSync(file, content);
console.log('Sidebar.vue updated');
