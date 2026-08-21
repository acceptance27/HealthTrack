<div class="patient-dashboard-shell">
    <section class="patient-welcome-banner">
        <div class="patient-profile-circle" aria-hidden="true">
            <svg viewBox="0 0 24 24">
                <circle cx="12" cy="8" r="4"/>
                <path d="M4.5 19.5c1.7-3.2 4.5-4.8 7.5-4.8s5.8 1.6 7.5 4.8"/>
            </svg>
        </div>

        <div class="patient-welcome-copy">
            <h1>Welcome, {{ $patient->first_name }}</h1>
            <p>Your records at {{ config('healthtrack.centre.name') }}.</p>
        </div>

        <button type="button" class="patient-portal-button">
            Patient Portal
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 17 17 7M9 7h8v8"/></svg>
        </button>
    </section>

    <div class="patient-metric-grid">
        <div class="patient-metric-card">
            <div class="patient-metric-icon">
                <svg viewBox="0 0 24 24"><path d="M7 3.75V7m10-3.25V7M4.5 10.5h15M6.75 5.5h10.5A2.25 2.25 0 0 1 19.5 7.75v9.5A2.25 2.25 0 0 1 17.25 19.5h-10.5A2.25 2.25 0 0 1 4.5 17.25v-9.5A2.25 2.25 0 0 1 6.75 5.5z"/></svg>
            </div>
            <h3>Upcoming Appointment</h3>
            <button type="button" class="patient-view-button">
                <svg viewBox="0 0 24 24"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6S2.5 12 2.5 12zm9.5 3a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/></svg>
                View Only
            </button>
        </div>

        <div class="patient-metric-card">
            <div class="patient-metric-icon">
                <svg viewBox="0 0 24 24"><path d="M3 12.5c1.3-3.6 4.7-5.9 9-5.9 4.3 0 7.7 2.3 9 5.9-1.3 3.6-4.7 5.9-9 5.9-4.3 0-7.7-2.3-9-5.9zm9-3.8v7.2m-3.6-3.6h7.2"/><path d="M19 5.5v4M17 7.5h4"/></svg>
            </div>
            <h3>Updated Vital Signs</h3>
            <button type="button" class="patient-view-button">
                <svg viewBox="0 0 24 24"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6S2.5 12 2.5 12zm9.5 3a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/></svg>
                View Only
            </button>
        </div>

        <div class="patient-metric-card">
            <div class="patient-metric-icon">
                <svg viewBox="0 0 24 24"><path d="M7 3.75A1.75 1.75 0 0 0 5.25 5.5v13A1.75 1.75 0 0 0 7 20.25h10A1.75 1.75 0 0 0 18.75 18.5v-10l-4.25-4.75H7zm7.75 1.56L17.69 7.5h-2.94A.75.75 0 0 1 14 6.75v-1.44zM8.5 10.5h7M8.5 13.5h7M8.5 16.5h4.5"/></svg>
            </div>
            <h3>Health Assessment</h3>
            <button type="button" class="patient-view-button">
                <svg viewBox="0 0 24 24"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6S2.5 12 2.5 12zm9.5 3a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/></svg>
                View Only
            </button>
        </div>

        <div class="patient-metric-card">
            <div class="patient-metric-icon">
                <svg viewBox="0 0 24 24"><path d="M12 3c3.7 0 6.7 3 6.7 6.7 0 4.8-6.7 10.8-6.7 10.8S5.3 14.5 5.3 9.7C5.3 6 8.3 3 12 3zm0 4.1a2.6 2.6 0 1 0 0 5.2 2.6 2.6 0 0 0 0-5.2z"/></svg>
            </div>
            <h3>Known Allergies</h3>
            <button type="button" class="patient-view-button">
                <svg viewBox="0 0 24 24"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6S2.5 12 2.5 12zm9.5 3a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/></svg>
                View Only
            </button>
        </div>
    </div>

    <section class="patient-panel patient-health-summary">
        <div class="patient-health-copy">
            <div class="patient-panel-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24"><path d="M7 3.75A1.75 1.75 0 0 0 5.25 5.5v13A1.75 1.75 0 0 0 7 20.25h10A1.75 1.75 0 0 0 18.75 18.5v-10l-4.25-4.75H7zm7.75 1.56L17.69 7.5h-2.94A.75.75 0 0 1 14 6.75v-1.44zM8.5 10.5h7M8.5 13.5h7M8.5 16.5h4.5"/></svg>
            </div>
            <h2>Your Health Information</h2>
            <p>Your Personal Information, Vital Signs, Health Assessment, Midwife Notes, Medical Histories and Allergies are all recorded by the Midwife and shown in one place!</p>
            <a href="{{ route('patient.my-health-information') }}" class="patient-summary-button">
                View My Health Information
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h12M13 5l7 7-7 7"/></svg>
            </a>
        </div>

        <div class="patient-health-illustration" aria-hidden="true">
            <svg viewBox="0 0 220 180">
                <g fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M78 30h52l32 32v76a16 16 0 0 1-16 16H78a16 16 0 0 1-16-16V46a16 16 0 0 1 16-16z" fill="rgba(13,74,62,0.06)"/>
                    <path d="M130 30v32h32"/>
                    <path d="M92 80h40M92 96h40M92 112h30"/>
                    <path d="M152 124h25c11 0 20-9 20-20v-5c0-11-9-20-20-20h-17a18 18 0 0 0-18 18v7c0 11 9 20 20 20z"/>
                    <path d="M156 108v36"/>
                    <path d="M146 116h26"/>
                    <path d="M138 132h44"/>
                    <path d="M175 60h18"/>
                    <path d="M175 72h18"/>
                    <path d="M68 131c10-11 23-16 38-16s28 5 38 16"/>
                </g>
                <circle cx="162" cy="98" r="23" fill="rgba(13,74,62,0.08)"/>
                <path d="M162 81v34M145 98h34" stroke="#0d4a3e" stroke-width="5" stroke-linecap="round"/>
            </svg>
        </div>
    </section>

    <section class="patient-panel patient-emergency-panel">
        <div class="patient-section-header">
            <div class="patient-panel-icon patient-panel-icon-small" aria-hidden="true">
                <svg viewBox="0 0 24 24"><path d="M12 3.5 5.7 6.3v5.7C5.7 16 8.7 19.2 12 20.5c3.3-1.3 6.3-4.5 6.3-8.5V6.3L12 3.5zm-1.4 6.8h2.8v2.8h-2.8zm0 4.2h2.8v2.1h-2.8z"/></svg>
            </div>
            <div>
                <h2>Emergency Contact</h2>
                <p>Important contacts and information in case of emergency.</p>
            </div>
        </div>

        <div class="patient-emergency-grid">
            <div class="patient-contact-card">
                <div class="patient-contact-icon">
                    <svg viewBox="0 0 24 24"><path d="M12 20.5s6.5-5.1 6.5-11.4A6.5 6.5 0 0 0 5.5 9.1c0 6.3 6.5 11.4 6.5 11.4zm0-8.8a2.7 2.7 0 1 1 0-5.4 2.7 2.7 0 0 1 0 5.4z"/></svg>
                </div>
                <div class="patient-contact-body">
                    <h3>Barangay Mambog I</h3>
                    <p>Brgy. Mambog I, Bacoor, Cavite</p>
                    <span>(046) 123-4567</span>
                </div>
            </div>

            <div class="patient-contact-card">
                <div class="patient-contact-icon">
                    <svg viewBox="0 0 24 24"><path d="M3.5 19.5h17M6 15.5v-7m6 7v-7m6 7v-7M5 7.5h14v7.5H5z"/></svg>
                </div>
                <div class="patient-contact-body">
                    <h3>City Government of Bacoor</h3>
                    <p>Bacoor City Hall, Bacoor, Cavite</p>
                    <span>(046) 417-3000</span>
                </div>
            </div>

            <div class="patient-contact-card">
                <div class="patient-contact-icon">
                    <svg viewBox="0 0 24 24"><path d="M7.5 4.5h2.5l1.1 4.2-1.9 1.6a12.9 12.9 0 0 0 6.3 6.3l1.6-1.9 4.2 1.1v2.5a2 2 0 0 1-2 2A15.8 15.8 0 0 1 5.5 6.5a2 2 0 0 1 2-2z"/></svg>
                </div>
                <div class="patient-contact-body">
                    <h3>Emergency Hotline</h3>
                    <p>(046) 123-4567</p>
                    <span>24/7 Available</span>
                </div>
            </div>

            <div class="patient-contact-card">
                <div class="patient-contact-icon">
                    <svg viewBox="0 0 24 24"><path d="M12 7.5v5.1l3.4 2.1M12 3.5a8.5 8.5 0 1 1 0 17 8.5 8.5 0 0 1 0-17z"/></svg>
                </div>
                <div class="patient-contact-body">
                    <h3>Office Hours</h3>
                    <p>Mon - Fri: 8:00 AM - 5:00 PM</p>
                    <span>(Closed on weekends and holidays)</span>
                </div>
            </div>
        </div>
    </section>

    <section class="patient-panel patient-health-tips">
        <div class="patient-tips-heading">
            <div class="patient-panel-icon patient-panel-icon-small" aria-hidden="true">
                <svg viewBox="0 0 24 24"><path d="M9 18h6M10.5 20h3M12 3.5A6.5 6.5 0 0 0 9 16.2c.8.8 1.6 1.3 3 1.3s2.2-.5 3-1.3A6.5 6.5 0 0 0 12 3.5zm0 2.3v3.4m-2.4 2.4h4.8"/></svg>
            </div>
            <div>
                <h2>Health Tips for You</h2>
                <p>Health is your Wealth!</p>
            </div>
        </div>

        <div class="patient-tip-grid">
            <div class="patient-tip-item">
                <div class="patient-tip-icon">
                    <svg viewBox="0 0 24 24"><path d="M12 3.5c3.5 3.8 6 6.4 6 9.4A6 6 0 0 1 6 12.9c0-3 2.5-5.6 6-9.4z"/></svg>
                </div>
                <span>Drink plenty<br>of water daily.</span>
            </div>
            <div class="patient-tip-item">
                <div class="patient-tip-icon">
                    <svg viewBox="0 0 24 24"><path d="M5 13.5 8.3 17l10.7-11"/></svg>
                </div>
                <span>Eat balanced<br>and healthy meals.</span>
            </div>
            <div class="patient-tip-item">
                <div class="patient-tip-icon">
                    <svg viewBox="0 0 24 24"><path d="M5 14.5c0-2.5 1.7-4.8 4.2-5.6 1-3.6 4.8-5.7 8.3-4.4 2.1 1 3.5 3.2 3.5 5.7 0 3.4-2.5 5.8-5.8 6.3v.5h-2.8v-4.5h2.1"/></svg>
                </div>
                <span>Stay active<br>and exercise.</span>
            </div>
            <div class="patient-tip-item">
                <div class="patient-tip-icon">
                    <svg viewBox="0 0 24 24"><path d="M19 15.5a6.5 6.5 0 0 1-11.4 4.3A.8.8 0 0 1 7.1 19l.4-1.8A7.7 7.7 0 0 1 5 10.9a7.1 7.1 0 0 1 14.1 0c0 2.1-.9 4-2.4 5.4l-.4.7v.5z"/></svg>
                </div>
                <span>Get enough rest<br>and sleep well.</span>
            </div>
        </div>
    </section>
</div>
