from PIL import Image, ImageDraw, ImageFont
import os

# Icon sizes yang dibutuhkan untuk PWA
sizes = [72, 96, 128, 144, 152, 192, 384, 512]

# Warna tema
bg_color = '#4F46E5'  # Indigo
text_color = 'white'

def hex_to_rgb(hex_color):
    hex_color = hex_color.lstrip('#')
    return tuple(int(hex_color[i:i+2], 16) for i in (0, 2, 4))

def create_icon(size):
    # Buat image dengan background
    img = Image.new('RGB', (size, size), hex_to_rgb(bg_color))
    draw = ImageDraw.Draw(img)
    
    # Hitung ukuran elemen
    margin = size // 4
    chart_width = size - (margin * 2)
    chart_height = size // 3
    
    # Gambar chart line
    line_width = max(2, size // 21)
    points = [
        (margin, margin + chart_height // 2),
        (margin + chart_width // 5, margin),
        (margin + chart_width * 2 // 5, margin + chart_height // 2),
        (margin + chart_width * 3 // 5, margin),
        (margin + chart_width * 4 // 5, margin + chart_height // 2),
        (margin + chart_width, margin)
    ]
    draw.line(points, fill='white', width=line_width, joint='curve')
    
    # Gambar vertical bars
    bar_count = 3
    bar_spacing = chart_width // (bar_count + 1)
    bar_bottom = size - margin
    bar_top = margin + chart_height + margin // 2
    
    for i in range(1, bar_count + 1):
        x = margin + bar_spacing * i
        draw.line([(x, bar_top), (x, bar_bottom)], fill='white', width=line_width)
    
    # Gambar dot
    dot_x = margin + chart_width * 2 // 5
    dot_y = margin + chart_height // 2
    dot_radius = size // 32
    draw.ellipse([dot_x - dot_radius, dot_y - dot_radius, 
                  dot_x + dot_radius, dot_y + dot_radius], fill='white')
    
    # Tambahkan text "MC"
    try:
        # Try to use a good font
        font_size = size // 8
        font = ImageFont.truetype("arial.ttf", font_size)
    except:
        # Fallback to default font
        font = ImageFont.load_default()
    
    text = "JB"
    # Get text bounding box
    bbox = draw.textbbox((0, 0), text, font=font)
    text_width = bbox[2] - bbox[0]
    text_height = bbox[3] - bbox[1]
    
    text_x = (size - text_width) // 2
    text_y = bar_bottom - margin // 2 - text_height // 2
    
    draw.text((text_x, text_y), text, fill='white', font=font)
    
    return img

# Buat folder icons jika belum ada
icons_dir = 'icons'
if not os.path.exists(icons_dir):
    os.makedirs(icons_dir)

# Generate semua icon
print("Generating PWA icons...")
for size in sizes:
    icon = create_icon(size)
    filename = f'{icons_dir}/icon-{size}x{size}.png'
    icon.save(filename, 'PNG')
    print(f"Created: {filename}")

print("\nAll icons generated successfully!")
print("Icons are saved in the 'icons' folder.")
