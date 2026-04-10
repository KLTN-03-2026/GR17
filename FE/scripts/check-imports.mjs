import fs from "fs";
import path from "path";

const rootDir = process.cwd();
const srcDir = path.join(rootDir, "src");
const moduleExportCache = new Map();

function walkFiles(dir, acc = []) {
  const entries = fs.readdirSync(dir, { withFileTypes: true });
  for (const entry of entries) {
    const fullPath = path.join(dir, entry.name);
    if (entry.isDirectory()) {
      walkFiles(fullPath, acc);
      continue;
    }

    if (/\.(vue|js|ts|mjs)$/.test(entry.name)) {
      acc.push(fullPath);
    }
  }
  return acc;
}

function parseImports(content) {
  const imports = [];
  const regex = /import\s+([\s\S]*?)\s+from\s+["']([^"']+)["']/g;
  let match;
  while ((match = regex.exec(content)) !== null) {
    imports.push({
      clause: String(match[1] || "").trim(),
      source: String(match[2] || "").trim(),
    });
  }
  return imports;
}

function resolveRelativeImport(filePath, importSource) {
  const basePath = path.resolve(path.dirname(filePath), importSource);
  const candidates = [
    basePath,
    `${basePath}.js`,
    `${basePath}.ts`,
    `${basePath}.mjs`,
    `${basePath}.vue`,
    path.join(basePath, "index.js"),
    path.join(basePath, "index.ts"),
    path.join(basePath, "index.vue"),
  ];

  return candidates.find((candidate) => fs.existsSync(candidate)) || null;
}

function parseNamedImports(clause) {
  const braceMatch = clause.match(/\{([^}]*)\}/);
  if (!braceMatch) return [];

  return String(braceMatch[1])
    .split(",")
    .map((part) => part.trim())
    .filter(Boolean)
    .map((part) => {
      if (part.includes(" as ")) {
        return part.split(/\s+as\s+/)[0].trim();
      }
      return part;
    });
}

function hasDefaultImport(clause) {
  if (!clause || clause.startsWith("{") || clause.startsWith("*")) {
    return false;
  }
  return true;
}

function readModuleExports(modulePath) {
  if (!modulePath || !fs.existsSync(modulePath)) {
    return { hasDefault: false, named: new Set() };
  }

  if (moduleExportCache.has(modulePath)) {
    return moduleExportCache.get(modulePath);
  }

  const content = fs.readFileSync(modulePath, "utf8");
  const named = new Set();

  let match;
  const directNamedRegex =
    /export\s+(?:(?:async\s+)?function|const|class|let|var)\s+([A-Za-z_$][\w$]*)/g;
  while ((match = directNamedRegex.exec(content)) !== null) {
    named.add(match[1]);
  }

  const groupedNamedRegex = /export\s*\{([^}]*)\}/g;
  while ((match = groupedNamedRegex.exec(content)) !== null) {
    const parts = String(match[1] || "")
      .split(",")
      .map((part) => part.trim())
      .filter(Boolean);
    for (const part of parts) {
      if (part.includes(" as ")) {
        const alias = part.split(/\s+as\s+/)[1]?.trim();
        if (alias) named.add(alias);
      } else {
        named.add(part);
      }
    }
  }

  const hasDefault = /export\s+default\s+/.test(content);
  const result = { hasDefault, named };
  moduleExportCache.set(modulePath, result);
  return result;
}

function validateImports(filePath, imports, errors) {
  for (const item of imports) {
    let resolvedSource = null;

    if (item.source.startsWith(".")) {
      resolvedSource = resolveRelativeImport(filePath, item.source);
      if (!resolvedSource) {
        errors.push(`${filePath}: import khong ton tai -> ${item.source}`);
      }
    }

    if (
      item.source.includes("services/httpClient") ||
      item.source.includes("services/appDialog")
    ) {
      const modulePath =
        resolvedSource || resolveRelativeImport(filePath, item.source);
      const exportInfo = readModuleExports(modulePath);

      if (hasDefaultImport(item.clause) && !exportInfo.hasDefault) {
        errors.push(
          `${filePath}: import default khong hop le tu ${item.source}. Module nay khong co default export.`,
        );
      }

      const namedImports = parseNamedImports(item.clause);
      for (const symbol of namedImports) {
        if (!exportInfo.named.has(symbol)) {
          errors.push(
            `${filePath}: named import ${symbol} khong ton tai trong ${item.source}.`,
          );
        }
      }
    }
  }
}

function main() {
  const args = process.argv.slice(2);
  const targets = args.length
    ? args.map((item) => path.resolve(rootDir, item))
    : walkFiles(srcDir);

  const errors = [];

  for (const filePath of targets) {
    if (!fs.existsSync(filePath)) {
      errors.push(`${filePath}: file khong ton tai.`);
      continue;
    }
    const content = fs.readFileSync(filePath, "utf8");
    const imports = parseImports(content);
    validateImports(filePath, imports, errors);
  }

  if (errors.length) {
    console.error("Phat hien import loi:");
    for (const error of errors) {
      console.error(`- ${error}`);
    }
    process.exit(1);
  }

  console.log(`OK: ${targets.length} file da duoc kiem tra import.`);
}

main();
