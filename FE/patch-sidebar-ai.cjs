const fs = require('fs');
const path = require('path');

const targetItem = `<router-link class="profile-panel__link" to="/khach-hang/ke-hoach">
            <i class="fas fa-calendar-days"></i>
            <span>Danh sách kế hoạch</span>
          </router-link>`;

const newItem = `
          <router-link class="profile-panel__link" to="/khach-hang/len-ke-hoach-ai">
            <i class="fas fa-magic"></i>
            <span>Lên kế hoạch AI</span>
          </router-link>`;

function patchFile(filePath) {
    let content = fs.readFileSync(filePath, 'utf8');
    
    if (content.includes('/khach-hang/len-ke-hoach-ai')) {
        console.log(`Skipping ${filePath} - already patched.`);
        return;
    }

    if (content.includes(targetItem)) {
        content = content.replace(targetItem, targetItem + newItem);
        fs.writeFileSync(filePath, content, 'utf8');
        console.log(`Patched ${filePath}`);
    } else {
        console.log(`Target item not found in ${filePath}`);
    }
}

function scanDir(dir) {
    const files = fs.readdirSync(dir);
    for (const file of files) {
        const fullPath = path.join(dir, file);
        if (fs.statSync(fullPath).isDirectory()) {
            scanDir(fullPath);
        } else if (fullPath.endsWith('.vue')) {
            patchFile(fullPath);
        }
    }
}

scanDir(path.join(__dirname, 'src/components/KhachHang'));
console.log("Done.");
