#!/usr/bin/env python3
from __future__ import annotations

import argparse
import re
import sys
from pathlib import Path


FE_ROOT = Path(__file__).resolve().parents[1]
WORKSPACE_ROOT = FE_ROOT.parent
BE_ROOT = WORKSPACE_ROOT / "DoAnTotNghiepBE"

SKIP_PARTS = {"node_modules", "dist", "vendor", ".git"}
SKIP_SUBSTRINGS = ("assets/startbootstrap-",)
TEXT_EXTENSIONS = {".vue", ".js", ".css", ".php", ".json", ".md", ".py"}

ALL_SCAN_ROOTS = [
    FE_ROOT / "src",
    BE_ROOT / "app",
    BE_ROOT / "routes",
    BE_ROOT / "resources",
    BE_ROOT / "database",
]

PARTNER_FILE_GLOBS = [
    "DoAnTotNghiepFE/src/components/DoiTac/**/*",
    "DoAnTotNghiepFE/src/router/index.js",
    "DoAnTotNghiepFE/src/layout/components/TopNavBar.vue",
    "DoAnTotNghiepBE/app/Http/Controllers/DoiTac*.php",
    "DoAnTotNghiepBE/app/Http/Requests/DoiTac*.php",
    "DoAnTotNghiepBE/app/Models/DoiTac.php",
    "DoAnTotNghiepBE/routes/api.php",
    "DoAnTotNghiepBE/database/seeders/DoiTac*.php",
    "DoAnTotNghiepBE/database/seeders/ChiTietTourSeeder.php",
    "DoAnTotNghiepBE/database/migrations/*doi_tac*.php",
    "DoAnTotNghiepBE/database/migrations/*utf8mb4*partner*.php",
    "DoAnTotNghiepBE/tests/Feature/DoiTac*.php",
]

# Catch common mojibake byte pairs while avoiding valid Vietnamese uppercase words
# like "MÃ", "ĐÃ", ...
MOJIBAKE_PATTERNS = (
    re.compile(r"Ã[\u0080-\u00BF]"),
    re.compile(r"Ä[\u0080-\u00BF\u2018\u2019\u201C\u201D]"),
    re.compile(r"á[\u0080-\u00BF]"),
    re.compile(r"â[\u0080-\u00BF]"),
)

BANNED_LABELS = (
    "AI Recommendation Engine",
    "Quick View",
    "Admin Panel",
    "AI-POWERED TRAVEL PLANNER",
)

NO_DIACRITIC_PATTERNS = (
    "Khong",
    "Vui long",
    "thanh cong",
    "That bai",
    "Cap nhat",
    "Xoa",
    "Them vao danh sach yeu thich",
    "danh sach yeu thich",
    "dia diem",
    "khach hang",
    "dang nhap",
    "mat khau",
)

COMMENT_BLOCK_PATTERN = re.compile(r"/\*.*?\*/|<!--.*?-->", re.DOTALL)
COMMENT_LINE_PATTERN = re.compile(r"^\s*(//|/\*|\*|<!--|-->)")


def should_skip(path: Path) -> bool:
    normalized = path.as_posix()
    if path.suffix.lower() not in TEXT_EXTENSIONS:
        return True
    if any(part in SKIP_PARTS for part in path.parts):
        return True
    return any(token in normalized for token in SKIP_SUBSTRINGS)


def strip_comments(text: str) -> str:
    text = COMMENT_BLOCK_PATTERN.sub("", text)
    lines = [line for line in text.splitlines() if not COMMENT_LINE_PATTERN.match(line)]
    return "\n".join(lines)


def audit_file(path: Path) -> list[tuple[int, str]]:
    findings: list[tuple[int, str]] = []
    text = path.read_text(encoding="utf-8")
    search_text = strip_comments(text)
    lines = search_text.splitlines()

    for pattern in MOJIBAKE_PATTERNS:
        for line_no, line in enumerate(lines, start=1):
            match = pattern.search(line)
            if match:
                findings.append((line_no, f"Mojibake token '{match.group(0)}' -> {line.strip()}"))

    for label in BANNED_LABELS:
        for line_no, line in enumerate(lines, start=1):
            if label in line:
                findings.append((line_no, f"English label is banned '{label}' -> {line.strip()}"))

    for phrase in NO_DIACRITIC_PATTERNS:
        pattern = re.compile(rf"(?<![A-Za-z]){re.escape(phrase)}(?![A-Za-z])")
        for line_no, line in enumerate(lines, start=1):
            if "_normalized" in line or "normalize" in line:
                continue
            if pattern.search(line):
                findings.append((line_no, f"No-diacritic phrase '{phrase}' -> {line.strip()}"))

    return findings


def iter_files_all() -> list[Path]:
    files: list[Path] = []
    for root in ALL_SCAN_ROOTS:
        if not root.exists():
            continue
        for path in root.rglob("*"):
            if path.is_file() and not should_skip(path):
                files.append(path.resolve())
    return files


def iter_files_partner() -> list[Path]:
    files: list[Path] = []
    for pattern in PARTNER_FILE_GLOBS:
        for path in WORKSPACE_ROOT.glob(pattern):
            if path.is_file() and not should_skip(path):
                files.append(path.resolve())
    return files


def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser(description="Audit UTF-8/mojibake and Vietnamese diacritics.")
    parser.add_argument(
        "--scope",
        choices=("partner", "all"),
        default="partner",
        help="Scan scope: partner module only or whole system.",
    )
    return parser.parse_args()


def main() -> int:
    if hasattr(sys.stdout, "reconfigure"):
        sys.stdout.reconfigure(encoding="utf-8", errors="backslashreplace")

    args = parse_args()
    files = iter_files_partner() if args.scope == "partner" else iter_files_all()
    files = sorted(set(files))

    all_findings: list[tuple[Path, int, str]] = []
    for path in files:
        try:
            findings = audit_file(path)
        except UnicodeDecodeError:
            all_findings.append((path, 1, "File is not readable as UTF-8"))
            continue

        for line_no, message in findings:
            all_findings.append((path, line_no, message))

    if all_findings:
        for path, line_no, message in all_findings:
            print(f"{path}:{line_no}: {message}")
        return 1

    print(f"Audit passed ({args.scope}): no UTF-8/mojibake or no-diacritic issues found.")
    return 0


if __name__ == "__main__":
    sys.exit(main())
