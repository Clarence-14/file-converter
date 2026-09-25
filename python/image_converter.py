"""
Image conversion module using Pillow and img2pdf.
Supports: JPG, PNG, WEBP, BMP, TIFF, GIF, ICO conversions and Image→PDF.
"""

import os
import sys
from PIL import Image
import img2pdf


def convert_image(input_path, output_path, conversion_type):
    """
    Convert an image file from one format to another.
    
    Args:
        input_path: Path to the source image file
        output_path: Path for the converted output file
        conversion_type: String like 'jpg-to-png', 'png-to-webp', etc.
    """
    parts = conversion_type.split('-to-')
    if len(parts) != 2:
        raise ValueError(f"Invalid conversion type: {conversion_type}")
    
    source_fmt, target_fmt = parts[0].lower(), parts[1].lower()
    
    # Image to PDF conversion
    if target_fmt == 'pdf':
        return image_to_pdf(input_path, output_path)
    
    # Open the image
    img = Image.open(input_path)
    
    # Handle format-specific requirements
    target_fmt_upper = target_fmt.upper()
    
    # Map common names to Pillow format names
    format_map = {
        'JPG': 'JPEG',
        'JPEG': 'JPEG',
        'PNG': 'PNG',
        'WEBP': 'WEBP',
        'BMP': 'BMP',
        'TIFF': 'TIFF',
        'TIF': 'TIFF',
        'GIF': 'GIF',
        'ICO': 'ICO',
    }
    
    pillow_format = format_map.get(target_fmt_upper)
    if not pillow_format:
        raise ValueError(f"Unsupported target format: {target_fmt}")
    
    # Convert RGBA to RGB for formats that don't support transparency
    if img.mode == 'RGBA' and pillow_format in ('JPEG', 'BMP', 'TIFF'):
        background = Image.new('RGB', img.size, (255, 255, 255))
        background.paste(img, mask=img.split()[3])
        img = background
    elif img.mode == 'P' and pillow_format == 'JPEG':
        img = img.convert('RGB')
    elif img.mode == 'LA' and pillow_format == 'JPEG':
        img = img.convert('RGB')
    elif img.mode != 'RGB' and pillow_format == 'JPEG':
        img = img.convert('RGB')
    
    # Set save parameters
    save_kwargs = {}
    if pillow_format == 'JPEG':
        save_kwargs['quality'] = 95
        save_kwargs['optimize'] = True
    elif pillow_format == 'PNG':
        save_kwargs['optimize'] = True
    elif pillow_format == 'WEBP':
        save_kwargs['quality'] = 90
        save_kwargs['method'] = 4
    elif pillow_format == 'ICO':
        # ICO needs specific sizes
        sizes = [(256, 256), (128, 128), (64, 64), (48, 48), (32, 32), (16, 16)]
        save_kwargs['sizes'] = sizes
    
    img.save(output_path, format=pillow_format, **save_kwargs)
    return True


def image_to_pdf(input_path, output_path):
    """Convert a single image to PDF using img2pdf."""
    try:
        # Try img2pdf first (lossless, preserves quality)
        with open(input_path, 'rb') as f:
            pdf_bytes = img2pdf.convert(f.read())
        with open(output_path, 'wb') as f:
            f.write(pdf_bytes)
    except Exception:
        # Fallback: use Pillow for formats img2pdf doesn't support
        img = Image.open(input_path)
        if img.mode == 'RGBA':
            background = Image.new('RGB', img.size, (255, 255, 255))
            background.paste(img, mask=img.split()[3])
            img = background
        elif img.mode != 'RGB':
            img = img.convert('RGB')
        img.save(output_path, 'PDF', resolution=150)
    return True


# Supported image conversion types
SUPPORTED_CONVERSIONS = [
    'jpg-to-png', 'png-to-jpg', 'jpg-to-webp', 'webp-to-jpg',
    'png-to-webp', 'webp-to-png', 'bmp-to-jpg', 'bmp-to-png',
    'jpg-to-bmp', 'png-to-bmp', 'tiff-to-jpg', 'tiff-to-png',
    'jpg-to-tiff', 'png-to-tiff', 'gif-to-png', 'gif-to-jpg',
    'png-to-gif', 'jpg-to-ico', 'png-to-ico',
    'jpg-to-pdf', 'png-to-pdf', 'webp-to-pdf', 'bmp-to-pdf',
]
