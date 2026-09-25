"""
Document conversion module using fpdf2, python-docx, openpyxl, PyPDF2, pdf2docx, PyMuPDF.
Supports: DOCX→PDF, XLSX→CSV, CSV→XLSX, TXT→PDF, PDF→TXT, PDF→DOCX, PDF→JPG, PDF→PNG
"""

import os
import csv
import sys
from fpdf import FPDF
from docx import Document
from openpyxl import Workbook, load_workbook
from PyPDF2 import PdfReader
import pymupdf as fitz
from pdf2docx import Converter as Pdf2DocxConverter
from docx2pdf import convert as docx2pdf_convert


def docx_to_pdf(input_path, output_path):
    """
    Convert DOCX to PDF using docx2pdf.
    Preserves all formatting but requires Microsoft Word installed.
    """
    # Use pythoncom for COM object initialization in new threads (required for local servers)
    import pythoncom
    pythoncom.CoInitialize()
    try:
        docx2pdf_convert(input_path, output_path)
    finally:
        pythoncom.CoUninitialize()
    return True


def xlsx_to_csv(input_path, output_path):
    """Convert XLSX spreadsheet to CSV format."""
    wb = load_workbook(input_path, read_only=True, data_only=True)
    ws = wb.active
    
    with open(output_path, 'w', newline='', encoding='utf-8') as f:
        writer = csv.writer(f)
        for row in ws.iter_rows(values_only=True):
            writer.writerow([cell if cell is not None else '' for cell in row])
    
    wb.close()
    return True


def csv_to_xlsx(input_path, output_path):
    """Convert CSV file to XLSX spreadsheet."""
    wb = Workbook()
    ws = wb.active
    ws.title = "Sheet1"
    
    with open(input_path, 'r', encoding='utf-8', errors='replace') as f:
        reader = csv.reader(f)
        for row_idx, row in enumerate(reader, 1):
            for col_idx, value in enumerate(row, 1):
                # Try to convert numeric values
                try:
                    value = int(value)
                except ValueError:
                    try:
                        value = float(value)
                    except ValueError:
                        pass
                ws.cell(row=row_idx, column=col_idx, value=value)
    
    wb.save(output_path)
    return True


def txt_to_pdf(input_path, output_path):
    """Convert plain text file to PDF."""
    pdf = FPDF()
    pdf.set_auto_page_break(auto=True, margin=15)
    pdf.add_page()
    pdf.set_font('Courier', size=10)
    
    with open(input_path, 'r', encoding='utf-8', errors='replace') as f:
        for line in f:
            safe_line = line.rstrip('\n').encode('latin-1', errors='replace').decode('latin-1')
            pdf.cell(0, 5, safe_line, new_x="LMARGIN", new_y="NEXT")
    
    pdf.output(output_path)
    return True


def pdf_to_txt(input_path, output_path):
    """Extract text from PDF file."""
    reader = PdfReader(input_path)
    text_parts = []
    
    for page in reader.pages:
        page_text = page.extract_text()
        if page_text:
            text_parts.append(page_text)
    
    with open(output_path, 'w', encoding='utf-8') as f:
        f.write('\n\n'.join(text_parts))
    
    return True


def pdf_to_docx(input_path, output_path):
    """Convert PDF to DOCX using pdf2docx."""
    cv = Pdf2DocxConverter(input_path)
    cv.convert(output_path)
    cv.close()
    return True


def pdf_to_jpg(input_path, output_path):
    """
    Render the first page of a PDF as a JPEG image using PyMuPDF.
    Uses 2x zoom (150 DPI equivalent) for quality.
    """
    doc = fitz.open(input_path)
    page = doc[0]
    mat = fitz.Matrix(2, 2)
    pix = page.get_pixmap(matrix=mat)
    pix.save(output_path, output="jpeg")
    doc.close()
    return True


def pdf_to_png(input_path, output_path):
    """
    Render the first page of a PDF as a PNG image using PyMuPDF.
    Uses 2x zoom (150 DPI equivalent) for quality.
    """
    doc = fitz.open(input_path)
    page = doc[0]
    mat = fitz.Matrix(2, 2)
    pix = page.get_pixmap(matrix=mat, alpha=False)
    pix.save(output_path, output="png")
    doc.close()
    return True


def convert_document(input_path, output_path, conversion_type):
    """
    Dispatch document conversion to the appropriate function.
    
    Args:
        input_path: Path to the source document
        output_path: Path for the converted output
        conversion_type: String like 'docx-to-pdf', 'xlsx-to-csv', etc.
    """
    converters = {
        'docx-to-pdf': docx_to_pdf,
        'xlsx-to-csv': xlsx_to_csv,
        'csv-to-xlsx': csv_to_xlsx,
        'txt-to-pdf': txt_to_pdf,
        'pdf-to-txt': pdf_to_txt,
        'pdf-to-docx': pdf_to_docx,
        'pdf-to-jpg': pdf_to_jpg,
        'pdf-to-png': pdf_to_png,
    }
    
    converter = converters.get(conversion_type)
    if not converter:
        raise ValueError(f"Unsupported document conversion: {conversion_type}")
    
    return converter(input_path, output_path)


# Supported document conversion types
SUPPORTED_CONVERSIONS = [
    'docx-to-pdf', 'xlsx-to-csv', 'csv-to-xlsx', 'txt-to-pdf', 'pdf-to-txt',
    'pdf-to-docx', 'pdf-to-jpg', 'pdf-to-png',
]
