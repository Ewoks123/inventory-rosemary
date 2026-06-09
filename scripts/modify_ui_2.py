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

            # More flexible regex to find the form-actions container and the Kembali button
            # We want to match:
            # <div class="... form-actions ..." style="...">
            #   <div>
            #       <h2>...</h2>
            #       <p>...</p>
            #   </div>
            #   <a href="..." class="button-link">Kembali</a>
            # </div>

            pattern = re.compile(
                r'(<div[^>]*class="[^"]*form-actions[^"]*"[^>]*>)\s*(<div[^>]*>.*?</div>)\s*(<a\s+href="[^"]*"\s+class="button-link"[^>]*>(?:&larr;\s*)?Kembali</a>)',
                re.IGNORECASE | re.DOTALL
            )
            
            def replace_form_actions(match):
                div_open = match.group(1)
                inner_div = match.group(2)
                back_btn = match.group(3)
                
                # Strip out existing justify-content and align-items if any
                div_open = re.sub(r'justify-content:\s*space-between;', '', div_open)
                div_open = re.sub(r'align-items:\s*center;', '', div_open)
                
                # Ensure it has flex column
                if 'style="' in div_open:
                    div_open = re.sub(r'style="([^"]*)"', r'style="\1 display: flex; flex-direction: column; align-items: flex-start; gap: 15px;"', div_open)
                else:
                    div_open = div_open.replace('class=', 'style="display: flex; flex-direction: column; align-items: flex-start; gap: 15px;" class=')
                
                if '&larr;' not in back_btn:
                    back_btn = back_btn.replace('>Kembali</a>', '>&larr; Kembali</a>')
                
                return f"{div_open}\n                    {back_btn}\n                    {inner_div}"

            new_content = pattern.sub(replace_form_actions, content)
            
            if new_content != content:
                content = new_content
                modified = True

            if modified:
                with open(path, "w", encoding="utf-8") as f:
                    f.write(content)
                print(f"Modified layout: {file}")

print("Done phase 2")
