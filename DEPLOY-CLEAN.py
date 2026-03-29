#!/usr/bin/env python3
"""Déploiement PAOLO avec nettoyage complet"""

import os
import ftplib
import tempfile

LOCAL_PATH = os.path.expanduser("~/.openclaw/workspace/projets/paolo/site")
FTP_HOST = "195.35.49.242"
FTP_USER = "u417457839"
FTP_PASS = "NewProduction18@H"
DOMAIN = "la-main-a-la-pate.online"

def deploy_ftp():
    print("🚀 DÉPLOIEMENT PAOLO (avec nettoyage)")
    print("=" * 40)
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        print("✅ Connecté")
        
        # Nettoyer TOUT le répertoire public_html
        print("🧹 Nettoyage distant...")
        ftp.cwd("/public_html")
        
        # Lister et supprimer tout
        files = ftp.nlst()
        for f in files:
            if f in ['.', '..']:
                continue
            try:
                # Essayer de supprimer fichier
                ftp.delete(f)
                print(f"  🗑️ Supprimé: {f}")
            except:
                try:
                    # C'est un dossier
                    ftp.rmd(f)
                    print(f"  🗑️ Supprimé dossier: {f}")
                except:
                    # Dossier non vide - on ignore pour l'instant
                    pass
        
        # Upload fichiers PAOLO
        print("📤 Upload Paolo...")
        
        def upload_dir(local_path, remote_path=""):
            for item in os.listdir(local_path):
                local_item = os.path.join(local_path, item)
                
                if os.path.isdir(local_item):
                    try:
                        ftp.mkd(item)
                        print(f"  📁 Créé: {item}")
                    except:
                        pass
                    ftp.cwd(item)
                    upload_dir(local_item, "")
                    ftp.cwd("..")
                else:
                    with open(local_item, 'rb') as f:
                        ftp.storbinary(f'STOR {item}', f)
                    print(f"  📄 Upload: {item}")
        
        upload_dir(LOCAL_PATH)
        ftp.quit()
        
        print("\n✅ DÉPLOIEMENT TERMINÉ")
        print(f"🌐 https://{DOMAIN}")
        return True
        
    except Exception as e:
        print(f"\n❌ Erreur: {e}")
        return False

if __name__ == "__main__":
    deploy_ftp()
