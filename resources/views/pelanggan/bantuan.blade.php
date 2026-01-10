@extends('layouts.master')

@section('content')
<style>
    .help-section {
        background: white;
        border-radius: 16px;
        padding: 25px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        margin-bottom: 20px;
    }
    .section-title {
        font-size: 1.2rem;
        font-weight: 600;
        color: #1a1a2e;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .section-title i {
        color: #667eea;
    }
    .faq-accordion .accordion-item {
        border: none;
        margin-bottom: 12px;
    }
    .faq-accordion .accordion-button {
        background: #f8f9ff;
        border-radius: 12px !important;
        font-weight: 500;
        color: #1a1a2e;
        padding: 18px 20px;
        box-shadow: none;
    }
    .faq-accordion .accordion-button:not(.collapsed) {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .faq-accordion .accordion-button::after {
        filter: brightness(0) saturate(100%);
    }
    .faq-accordion .accordion-button:not(.collapsed)::after {
        filter: brightness(0) invert(1);
    }
    .faq-accordion .accordion-body {
        padding: 20px;
        background: #fafbff;
        border-radius: 0 0 12px 12px;
        color: #555;
        line-height: 1.7;
    }
    .contact-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 16px;
        padding: 30px;
        text-align: center;
    }
    .contact-card h5 {
        margin-bottom: 15px;
    }
    .contact-card .contact-methods {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 15px;
        margin-top: 20px;
    }
    .contact-btn {
        background: rgba(255,255,255,0.2);
        color: white;
        border: none;
        padding: 12px 25px;
        border-radius: 12px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        text-decoration: none;
    }
    .contact-btn:hover {
        background: rgba(255,255,255,0.3);
        color: white;
        transform: translateY(-3px);
    }
    .contact-btn.whatsapp {
        background: #25D366;
    }
    .contact-btn.whatsapp:hover {
        background: #1da851;
    }
    .quick-links {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-top: 20px;
    }
    .quick-link-card {
        background: #f8f9ff;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        color: #1a1a2e;
    }
    .quick-link-card:hover {
        background: white;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        transform: translateY(-3px);
        color: #667eea;
    }
    .quick-link-card i {
        font-size: 2rem;
        color: #667eea;
        margin-bottom: 10px;
    }
    .quick-link-card span {
        display: block;
        font-weight: 500;
    }
    .jam-operasional {
        background: #fff3cd;
        border-radius: 12px;
        padding: 20px;
        margin-top: 20px;
    }
    .jam-operasional h6 {
        color: #856404;
        margin-bottom: 10px;
    }
    .jam-operasional p {
        color: #856404;
        margin: 0;
    }
    
    /* Mobile Responsive */
    @media (max-width: 991px) {
        .col-lg-4 {
            margin-top: 20px;
        }
    }
    
    @media (max-width: 768px) {
        .help-section {
            padding: 15px;
        }
        .section-title {
            font-size: 1rem;
        }
        .faq-accordion .accordion-button {
            padding: 12px 15px;
            font-size: 0.9rem;
        }
        .faq-accordion .accordion-body {
            padding: 15px;
            font-size: 0.9rem;
        }
        .quick-links {
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }
        .quick-link-card {
            padding: 15px;
        }
        .quick-link-card i {
            font-size: 1.5rem;
        }
        .quick-link-card span {
            font-size: 0.85rem;
        }
        .contact-card {
            padding: 20px;
        }
        .contact-card i {
            font-size: 2.5rem !important;
        }
        .jam-operasional {
            padding: 15px;
        }
        h4.fw-bold {
            font-size: 1.2rem;
        }
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bx bx-help-circle me-2"></i> Bantuan
            </h4>
            <p class="text-muted mb-0">Pertanyaan umum dan hubungi kami</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- FAQ Section -->
            <div class="help-section">
                <div class="section-title">
                    <i class="bx bx-question-mark"></i>
                    Pertanyaan Umum (FAQ)
                </div>
                
                <div class="accordion faq-accordion" id="faqAccordion">
                    @foreach($faq as $index => $item)
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" type="button" 
                                    data-bs-toggle="collapse" data-bs-target="#faq{{ $index }}">
                                {{ $item->pertanyaan }}
                            </button>
                        </h2>
                        <div id="faq{{ $index }}" class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" 
                             data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                {{ $item->jawaban }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Quick Links -->
            <div class="help-section">
                <div class="section-title">
                    <i class="bx bx-link"></i>
                    Link Cepat
                </div>
                <div class="quick-links">
                    <a href="{{ route('pelanggan.tagihan') }}" class="quick-link-card">
                        <i class="bx bx-credit-card"></i>
                        <span>Bayar Tagihan</span>
                    </a>
                    <a href="{{ route('tagihan.riwayat_pembayaran') }}" class="quick-link-card">
                        <i class="bx bx-history"></i>
                        <span>Riwayat Pembayaran</span>
                    </a>
                    <a href="{{ route('profile') }}" class="quick-link-card">
                        <i class="bx bx-user"></i>
                        <span>Edit Profile</span>
                    </a>
                    <a href="{{ route('pelanggan.pemakaian') }}" class="quick-link-card">
                        <i class="bx bx-bar-chart-alt-2"></i>
                        <span>Lihat Pemakaian</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Contact Section -->
            <div class="contact-card">
                <i class="bx bx-headphone" style="font-size: 3rem; margin-bottom: 15px;"></i>
                <h5>Butuh Bantuan?</h5>
                <p style="opacity: 0.9;">Tim kami siap membantu Anda 24/7</p>
                
                <div class="contact-methods">
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $kontakCS) }}" 
                       class="contact-btn whatsapp" target="_blank">
                        <i class="bx bxl-whatsapp"></i>
                        WhatsApp
                    </a>
                </div>
                
                <div class="jam-operasional">
                    <h6><i class="bx bx-time me-1"></i> Jam Operasional</h6>
                    <p><strong>Senin - Sabtu:</strong> 08.00 - 21.00 WIB</p>
                    <p><strong>Minggu:</strong> 09.00 - 17.00 WIB</p>
                </div>
            </div>

            <!-- Tips -->
            <div class="help-section mt-3">
                <div class="section-title">
                    <i class="bx bx-bulb"></i>
                    Tips
                </div>
                <ul style="color: #555; line-height: 2;">
                    <li>Bayar tagihan sebelum jatuh tempo</li>
                    <li>Restart router jika koneksi lambat</li>
                    <li>Simpan bukti pembayaran</li>
                    <li>Update password secara berkala</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
