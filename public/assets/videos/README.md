# Videos

Videodateien werden **nicht** ins Git-Repository eingecheckt (`.gitignore`).  
Dieses Verzeichnis enthält nur diese Anleitung.

---

## Video herunterladen (yt-dlp)

```bash
# Installieren (einmalig)
pip install yt-dlp

# Video herunterladen (beste Qualität)
yt-dlp -o "%(title)s.%(ext)s" <URL>
```

---

## In Web-Formate konvertieren (ffmpeg)

Ziel: `<name>_web.mp4` (H.264) und `<name>_web.webm` (VP9) für maximale Browser-Kompatibilität.

```bash
# MP4 (H.264, breite Kompatibilität)
ffmpeg -i original.mp4 \
  -vcodec libx264 -crf 23 -preset slow \
  -acodec aac -b:a 128k \
  -vf "scale=-2:720" \
  video_web.mp4

# WebM (VP9, kleinere Dateigröße)
ffmpeg -i original.mp4 \
  -vcodec libvpx-vp9 -crf 30 -b:v 0 \
  -acodec libopus -b:a 96k \
  -vf "scale=-2:720" \
  video_web.webm
```

---

## In `content/home/videos.json` eintragen

```json
[
    {
        "enabled": true,
        "file": "video_web",
        "title": "Titel des Videos",
        "description": "Kurze Beschreibung (optional)"
    }
]
```

Der `file`-Wert ohne Extension — das Template ergänzt `.webm` und `.mp4` automatisch.

---

## Deployment

Videodateien sind zu groß für Git. Auf den Server übertragen via:

```bash
scp video_web.mp4 video_web.webm user@server:/var/www/<domain>/public/assets/videos/
```
