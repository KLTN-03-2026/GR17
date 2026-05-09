const fs = require('fs');
let content = fs.readFileSync('src/components/DoiTac/HanhTrinhTour.vue', 'utf8');

// replace window.confirm
content = content.replace(
  'if (!window.confirm("Bạn có chắc muốn xóa điểm này khỏi hành trình?")) return;',
  `const confirmed = await showConfirm({
        title: "Xác nhận xóa",
        message: "Bạn có chắc muốn xóa điểm này khỏi hành trình?",
        tone: "danger",
        confirmText: "Xóa",
      });
      if (!confirmed) return;`
);

// add showConfirm import
content = content.replace(
  '} from "./shared/partnerApi";',
  `} from "./shared/partnerApi";\nimport { showConfirm } from "../../services/appDialog.js";`
);

fs.writeFileSync('src/components/DoiTac/HanhTrinhTour.vue', content);
