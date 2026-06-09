import os
import re

base_dir = r"c:\Users\user\Music\InventoryRosemary - Copy\InventoryRosemary - Copy\InventoryRosemary - Copy"

for root, dirs, files in os.walk(base_dir):
    for file in files:
        if file.endswith(".html"):
            path = os.path.join(root, file)
            with open(path, "r", encoding="utf-8") as f:
                content = f.read()

            modified = False

            # 1. Update logo section to be clickable
            if "logo-section" in content:
                rel_path = "index.html" if "admin" in root else "../admin/index.html"
                
                # Replace the div tag keeping its classes but adding our onclick
                new_content = re.sub(
                    r'<div\s+class="logo-section"[^>]*>', 
                    f'<div class="logo-section" style="cursor: pointer; display: flex; align-items: center; gap: 10px;" onclick="window.location.href=\'{rel_path}\'">', 
                    content
                )
                if new_content != content:
                    content = new_content
                    modified = True

            # 2. Fix the "Laporan Keluar" -> "Summary"
            if "stokmaterial-menu.html" in file:
                if ">Laporan Keluar</a>" in content:
                    content = content.replace(">Laporan Keluar</a>", ">Summary</a>")
                    modified = True
            if "stokmaterial-report.html" in file:
                if "<h2>Laporan Material Keluar</h2>" in content:
                    content = content.replace("<h2>Laporan Material Keluar</h2>", "<h2>Summary Material</h2>")
                    modified = True
                if "<title>Rosemary Nutrition - Laporan Material</title>" in content:
                    content = content.replace("<title>Rosemary Nutrition - Laporan Material</title>", "<title>Rosemary Nutrition - Summary Material</title>")
                    modified = True

            # 3. Move the "Kembali" button to the top left in form-actions
            # We look for something like:
            # <div class="form-actions"[^>]*>
            #    <div>
            #        <h2>...</h2>
            #        <p>...</p>
            #    </div>
            #    <a href="..." class="button-link">Kembali</a>
            # </div>
            
            # Since HTML structure can vary, we use regex to capture the parts
            # Part 1: <div class="form-actions"...>
            # Part 2: everything before the <a ...>Kembali</a> (usually the div with h2)
            # Part 3: <a ...>Kembali</a>
            
            pattern = re.compile(
                r'(<div\s+class="form-actions"[^>]*>)\s*(<div[^>]*>.*?</div>)\s*(<a\s+href="[^"]*"\s+class="button-link"[^>]*>Kembali</a>)',
                re.IGNORECASE | re.DOTALL
            )
            
            def replace_form_actions(match):
                div_open = match.group(1)
                inner_div = match.group(2)
                back_btn = match.group(3)
                
                # Replace inline style of form-actions to make it flex column
                div_open = re.sub(r'style="[^"]*"', 'style="display: flex; flex-direction: column; align-items: flex-start; gap: 15px;"', div_open)
                if 'style="' not in div_open:
                    div_open = div_open.replace('class="form-actions"', 'class="form-actions" style="display: flex; flex-direction: column; align-items: flex-start; gap: 15px;"')
                
                # Prepend an arrow to the back button
                back_btn = back_btn.replace('>Kembali</a>', '>&larr; Kembali</a>')
                
                return f"{div_open}\n                    {back_btn}\n                    {inner_div}"

            new_content = pattern.sub(replace_form_actions, content)
            
            # Sometimes the Kembali button is BEFORE the inner div, or there is no form-actions
            # Let's also check if there is a button that says Kembali but it wasn't matched above
            if new_content == content and "Kembali</a>" in content:
                # If it didn't match the specific form-actions structure, let's just make sure it has an arrow at least
                if ">Kembali</a>" in content:
                    pass
                    
            if new_content != content:
                content = new_content
                modified = True

            if modified:
                with open(path, "w", encoding="utf-8") as f:
                    f.write(content)
                print(f"Modified: {file}")

print("Done")
