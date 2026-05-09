import assert from "node:assert/strict";
import fs from "node:fs";
import path from "node:path";

const projectRoot = path.resolve(import.meta.dirname, "..");
const editComponent = fs.readFileSync(
  path.join(projectRoot, "src/components/KhachHang/KeHoach/ChinhSuaKeHoach.vue"),
  "utf8",
);
const detailComponent = fs.readFileSync(
  path.join(projectRoot, "src/components/KhachHang/KeHoach/ChiTietKeHoach.vue"),
  "utf8",
);

assert.match(editComponent, /generation_mode/);
assert.match(editComponent, /notice/);
assert.match(editComponent, /generationNotice/);
assert.match(editComponent, /aiProgressPercent/);
assert.match(editComponent, /aiProgressStage/);
assert.match(editComponent, /cleanupAiProgress/);
assert.match(detailComponent, /generation_mode/);
assert.match(detailComponent, /notice/);
assert.match(detailComponent, /generationNotice/);
assert.match(detailComponent, /aiProgressPercent/);
assert.match(detailComponent, /aiProgressStage/);
assert.match(detailComponent, /cleanupAiProgress/);

console.log("OK: itinerary AI fallback UI hooks passed.");
