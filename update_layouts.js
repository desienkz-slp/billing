const fs = require('fs');
const path = require('path');

function walkDir(dir, callback) {
  fs.readdirSync(dir).forEach(f => {
    let dirPath = path.join(dir, f);
    let isDirectory = fs.statSync(dirPath).isDirectory();
    isDirectory ? walkDir(dirPath, callback) : callback(path.join(dir, f));
  });
}

let modified = 0;
walkDir('resources/js/Pages', function (filePath) {
  if (filePath.endsWith('.vue')) {
    let content = fs.readFileSync(filePath, 'utf8');
    let original = content;

    // Replace max-w-* with w-full h-full flex flex-col for main page wrappers
    content = content.replace(/(<div[^>]*class="[^"]*)(max-w-7xl|max-w-9xl|max-w-5xl|max-w-4xl|max-w-3xl)([^"]*")/g, (match, p1, p2, p3) => {
      // Ignore if it's inside a modal (usually has align-bottom, shadow-xl, transform, text-left, modal)
      if (match.includes('align-bottom') || match.includes('transform') || match.includes('shadow-xl') || match.includes('modal') || match.includes('max-h-')) {
        return match;
      }

      let newClass = p1 + 'max-w-full w-full' + p3;
      if (!newClass.includes('h-full')) {
        newClass = newClass.replace('max-w-full', 'max-w-full h-full flex flex-col min-h-0');
      }
      // Replace py-8 with p-4 to give it compact screen edge padding instead of massive vertical padding
      newClass = newClass.replace(/py-8/g, 'p-4');
      return newClass;
    });

    if (original !== content) {
      fs.writeFileSync(filePath, content, 'utf8');
      modified++;
      console.log('Modified: ' + filePath);
    }
  }
});
console.log('Total modified: ' + modified);
