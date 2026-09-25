"""
Main converter dispatcher - CLI entry point for all file conversions.

Usage: python converter.py <input_file> <output_file> <conversion_type>

Exit codes:
    0 = Success
    1 = Error (message printed to stderr)
"""

import sys
import os

# Add the script's directory to the path
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))

from image_converter import convert_image, SUPPORTED_CONVERSIONS as IMAGE_CONVERSIONS
from document_converter import convert_document, SUPPORTED_CONVERSIONS as DOC_CONVERSIONS


def main():
    if len(sys.argv) != 4:
        print("Usage: python converter.py <input_file> <output_file> <conversion_type>", file=sys.stderr)
        sys.exit(1)
    
    input_file = sys.argv[1]
    output_file = sys.argv[2]
    conversion_type = sys.argv[3].lower()
    
    # Validate input file exists
    if not os.path.exists(input_file):
        print(f"Error: Input file not found: {input_file}", file=sys.stderr)
        sys.exit(1)
    
    # Ensure output directory exists
    output_dir = os.path.dirname(output_file)
    if output_dir and not os.path.exists(output_dir):
        os.makedirs(output_dir, exist_ok=True)
    
    try:
        if conversion_type in IMAGE_CONVERSIONS:
            convert_image(input_file, output_file, conversion_type)
        elif conversion_type in DOC_CONVERSIONS:
            convert_document(input_file, output_file, conversion_type)
        else:
            print(f"Error: Unsupported conversion type: {conversion_type}", file=sys.stderr)
            print(f"Supported image conversions: {', '.join(IMAGE_CONVERSIONS)}", file=sys.stderr)
            print(f"Supported document conversions: {', '.join(DOC_CONVERSIONS)}", file=sys.stderr)
            sys.exit(1)
        
        # Verify output was created
        if not os.path.exists(output_file):
            print("Error: Conversion completed but output file was not created", file=sys.stderr)
            sys.exit(1)
        
        print(f"OK: {os.path.basename(output_file)}")
        sys.exit(0)
        
    except Exception as e:
        print(f"Error: {str(e)}", file=sys.stderr)
        sys.exit(1)


if __name__ == '__main__':
    main()
