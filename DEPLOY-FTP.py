#!/usr/bin/env python3
"""Déploiement PAOLO via FTP"""

import os
import ftplib
import tempfile
import subprocess

LOCAL_PATH = os.path.expanduser("~/.openclaw/workspace/projets/paolo")
FTP_HOST = "195.35.49.242"
FTP_USER = "u417457839"  # Sans suffixe domaine
FTP_PASS = "NewProduction18@H"
DOMAIN = "la-main-a-la-pate.online"

def build_project():
    print("[PAOLO 1/5] Build...")
    os.chdir(LOCAL_PATH)
    
    if os.path.exists("composer.json"):
        subprocess.run(["composer", "install", "--no-dev", "--optimize-autoloader", "-q"], 
                       capture_output=True, text=True)
        print("  ✅ Composer")
    
    if os.path.exists("package.json"):
        subprocess.run(["npm", "ci", "-q"], capture_output=True, text=True)
        subprocess.run(["npm", "run", "build"], capture_output=True, text=True)
        print("  ✅ NPM")
    return True

def prepare_deploy_files():
    print("[PAOLO 2/5] Préparation...")
    temp_dir = tempfile.mkdtemp()
    deploy_dir = os.path.join(temp_dir, "deploy")
    os.makedirs(deploy_dir, exist_ok=True)
    
    # Pour Paolo, copier le dossier site/ ou tout si Laravel
    if os.path.exists(os.path.join(LOCAL_PATH, "site")):
        os.system(f'cp -r "{LOCAL_PATH}/site/*" "{deploy_dir}/" 2>/dev/null')
    else:
        files = ["app", "bootstrap", "config", "database", "public", "resources", 
                 "routes", "storage", "vendor", "artisan", ".env.production"]
        for item in files:
            src = os.path.join(LOCAL_PATH, item)
            dst = os.path.join(deploy_dir, item.replace(".production", ""))
            if os.path.isdir(src):
                os.system(f'cp -r "{src}" "{dst}" 2>/dev/null')
            elif os.path.isfile(src):
                os.system(f'cp "{src}" "{dst}" 2>/dev/null')
    
    return deploy_dir

def deploy_ftp(deploy_dir):
    print("[PAOLO 3-4/5] FTP...")
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd("/public_html")
        
        def upload_recursive(local_path):
            for item in os.listdir(local_path):
                local_item = os.path.join(local_path, item)
                if os.path.isdir(local_item):
                    try: ftp.mkd(item)
                    except: pass
                    ftp.cwd(item)
                    upload_recursive(local_item)
                    ftp.cwd("..")
                else:
                    with open(local_item, 'rb') as f:
                        ftp.storbinary(f'STOR {item}', f)
        
        upload_recursive(deploy_dir)
        ftp.quit()
        print("  ✅ Upload terminé")
        return True
    except Exception as e:
        print(f"  ❌ Erreur: {e}")
        return False

def health_check():
    print("[PAOLO 5/5] Health check...")
    import time
    time.sleep(2)
    result = subprocess.run(
        ['curl', '-s', '-o', '/dev/null', '-w', '%{http_code}', f'https://{DOMAIN}/'],
        capture_output=True, text=True
    )
    status = result.stdout.strip()
    if status == "200":
        print(f"  ✅ Site OK (HTTP {status})")
    else:
        print(f"  ⚠️ HTTP {status}")

if __name__ == "__main__":
    print("🚀 DÉPLOIEMENT PAOLO")
    print("=" * 40)
    build_project()
    deploy_dir = prepare_deploy_files()
    if deploy_ftp(deploy_dir):
        health_check()
        print("\n✅ PAOLO TERMINÉ")
    else:
        print("\n❌ ÉCHEC")
