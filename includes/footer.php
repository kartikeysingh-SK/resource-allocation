<footer class="footer-bar no-print">
    <div class="container-fluid px-3 px-xl-4 d-flex flex-wrap justify-content-between align-items-center gap-4" style="max-width: 1680px; margin: 0 auto;">
        <div class="d-flex align-items-center gap-3">
            <div class="brand-badge" style="width:34px;height:34px;font-size:1.1rem;">R</div>
            <div>
                <strong style="color:var(--brand-charcoal);font-family:'Playfair Display',serif;">RTRAE <span class="text-muted fw-normal">/</span> Real-Time Resource Allocation Engine</strong>
                <div class="small text-muted">Universal Campus, Laboratory &amp; Computational Facility Scheduling</div>
            </div>
        </div>
        <div class="d-flex flex-wrap gap-4 small">
            <div><i class="bi bi-telephone-fill me-1" style="color:var(--brand-bronze);"></i> <a href="tel:7048700224" class="fw-semibold text-decoration-none" style="color:var(--brand-forest);font-family:'JetBrains Mono',monospace;">+91 7048700224</a></div>
            <div><i class="bi bi-envelope-fill me-1" style="color:var(--brand-forest);"></i> <a href="mailto:25amtics@gmail.com" class="fw-semibold text-decoration-none" style="color:var(--brand-forest);font-family:'JetBrains Mono',monospace;">25amtics@gmail.com</a></div>
        </div>
    </div>
</footer>

<!-- Global RTRAE Intelligent Webpage Knowledge Chatbot -->
<div id="aiChatWidget" class="no-print" style="position:fixed;bottom:24px;right:24px;z-index:999999;">
    <!-- Floating Trigger Button -->
    <button id="aiToggleBtn" type="button" onclick="openGlobalAiChat(event)" class="shadow-lg" 
            style="width:58px;height:58px;border-radius:50%;background:#315F43;color:#FFFDF7;border:2px solid rgba(255,255,255,0.4);box-shadow:0 10px 28px rgba(49,95,67,0.35);font-size:1.45rem;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:transform 0.25s ease;"
            onmouseover="this.style.transform='scale(1.08)'" onmouseout="this.style.transform='scale(1)'"
            aria-label="Open RTRAE Assistant">
        <i class="bi bi-robot"></i>
    </button>

    <!-- Chat Window Container -->
    <div id="aiChatBox" class="shadow-2xl" 
         style="width:390px;max-width:calc(100vw - 32px);height:535px;max-height:calc(100vh - 48px);border-radius:22px;background:#FFFDF7;border:1px solid rgba(49,95,67,0.25);box-shadow:0 25px 70px -15px rgba(23,27,24,0.35);display:none;flex-direction:column;overflow:hidden;animation:chatSlideUp 0.3s cubic-bezier(0.16,1,0.3,1) both;">
        
        <!-- Header with Guaranteed Functional Close & Minimize Buttons -->
        <div class="d-flex justify-content-between align-items-center py-3 px-3.5" 
             style="background:#171B18;color:#F6F3E9;border-bottom:1px solid rgba(49,95,67,0.3);">
            <div class="d-flex align-items-center gap-2.5">
                <div class="rounded-2 p-1 d-flex align-items-center justify-content-center text-white" style="width:30px;height:30px;background:#315F43;font-size:0.9rem;border-radius:8px;">
                    <i class="bi bi-stars"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold" style="font-family:'Playfair Display',serif;font-size:0.95rem;color:#FFFDF7;">RTRAE Assistant</h6>
                    <small style="color:#B8CBB1;font-size:0.72rem;"><span style="color:#34D399;margin-right:3px;">●</span> Webpage Knowledge</small>
                </div>
            </div>
            
            <!-- Guaranteed Functional Controls: Minimize & Close ("Cut") -->
            <div class="d-flex align-items-center gap-1.5">
                <button type="button" id="aiChatMinBtn" onclick="closeGlobalAiChat(event)" title="Minimize Chatbot" aria-label="Minimize Assistant" 
                        style="background:rgba(255,255,255,0.12);border:1px solid rgba(255,255,255,0.2);color:#FFFDF7;width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:14px;font-weight:bold;transition:all 0.2s;" 
                        onmouseover="this.style.background='rgba(255,255,255,0.25)';" 
                        onmouseout="this.style.background='rgba(255,255,255,0.12)';">
                    —
                </button>
                <button type="button" id="aiChatCloseBtn" onclick="closeGlobalAiChat(event)" title="Close / Cut Chatbot (Esc)" aria-label="Close Assistant" 
                        style="background:rgba(220,38,38,0.25);border:1px solid rgba(220,38,38,0.45);color:#FFFDF7;width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:14px;font-weight:bold;transition:all 0.2s;" 
                        onmouseover="this.style.background='#DC2626';this.style.transform='scale(1.05)';" 
                        onmouseout="this.style.background='rgba(220,38,38,0.25)';this.style.transform='scale(1)';">
                    ✕
                </button>
            </div>
        </div>

        <!-- Chat Messages Feed -->
        <div id="aiChatMessages" class="p-3 flex-grow-1 overflow-auto" style="font-size:0.83rem;scroll-behavior:smooth;background:#FFFDF7;">
            <!-- Initial content populated by JS -->
        </div>

        <!-- Chat Input Footer -->
        <div class="p-2.5" style="background:#F6F3E9;border-top:1px solid rgba(49,95,67,0.15);">
            <div class="d-flex align-items-center gap-1.5 bg-white rounded-3 px-2 py-1 shadow-sm" style="border:1px solid rgba(49,95,67,0.25);">
                <input type="text" id="aiChatInput" class="form-control form-control-sm border-0 shadow-none px-1" 
                       placeholder="Ask about classrooms, labs, formula, etc…" 
                       style="background:transparent!important;color:#252B25!important;font-size:0.84rem;"
                       autocomplete="off" aria-label="Ask assistant">
                <button type="button" class="btn btn-sm px-3 fw-bold" onclick="sendGlobalAiChat()" 
                        style="background:#315F43;color:#FFFDF7;border-radius:8px;">
                    Send
                </button>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes chatSlideUp {
    from { opacity: 0; transform: translateY(18px) scale(0.97); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}
.ai-bubble {
    border-radius: 14px;
    padding: 10px 13px;
    line-height: 1.45;
    word-break: break-word;
    max-width: 88%;
}
.ai-bubble-bot {
    background: #F6F3E9;
    color: #252B25;
    border: 1px solid rgba(49, 95, 67, 0.16);
}
.ai-bubble-user {
    background: #315F43;
    color: #FFFDF7;
    margin-left: auto;
}
.ai-chip {
    background: rgba(49, 95, 67, 0.08);
    border: 1px solid rgba(49, 95, 67, 0.25);
    color: #315F43;
    font-size: 0.72rem;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 20px;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-block;
    margin: 3px 2px;
}
.ai-chip:hover {
    background: #315F43;
    color: #FFFDF7;
    transform: translateY(-1px);
}
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
/* Comprehensive Webpage Knowledge Base */
const RTRAE_KB = [
    {
        keywords: ['hello', 'hi', 'hey', 'start', 'help', 'who'],
        reply: `Hello! I am your <strong>RTRAE Assistant</strong>. I have complete knowledge of this platform and webpage. Ask me anything about classrooms (B01–B13), computer labs, the suitability formula, or how to allocate tasks!`,
        chips: ['Classroom Details', 'Computer Lab Specs', 'Scoring Formula', 'How to Allocate']
    },
    {
        keywords: ['classroom', 'classrooms', 'b01', 'b02', 'b03', 'b11', 'b12', 'b13', 'lecture', 'halls', 'rooms'],
        reply: `<strong>🏫 Classrooms B01–B03 &amp; B11–B13:</strong><br>
        • <strong>Capacity</strong>: Tiered lecture halls seating 60 to 120 students.<br>
        • <strong>Technology</strong>: Dual 86" 4K interactive smartboards, ceiling digital projectors, motorized podiums.<br>
        • <strong>Environment</strong>: Centralized automated inverter climate control.<br>
        • <strong>Turnaround</strong>: Automated 10-minute sanitation buffer between consecutive classes.`,
        chips: ['Computer Lab Specs', 'How to Allocate', 'Scheduling Calendar']
    },
    {
        keywords: ['lab', 'labs', 'computer', 'gpu', 'workstation', 'rig', 'specs', 'servers'],
        reply: `<strong>💻 Advanced Computing &amp; AI Labs (101–104):</strong><br>
        • <strong>Workstations</strong>: 64 systems per lab with Intel Core i9 CPUs, 32GB DDR5 RAM, and 1TB NVMe.<br>
        • <strong>GPU Compute Cluster</strong>: NVIDIA RTX 4080 (16GB) nodes for AI batch jobs, Docker, PyTorch &amp; CUDA.<br>
        • <strong>Network &amp; Power</strong>: 10 Gbps low-latency fiber with 4-hr industrial online UPS backup.`,
        chips: ['Classroom Details', 'Scoring Formula', 'NLP Auto-Allocate']
    },
    {
        keywords: ['formula', 'score', 'scoring', 'suitability', 'algorithm', 'math', 'weights'],
        reply: `<strong>📐 Suitability Score Formula:</strong><br>
        <code>S = 0.50K + 0.30A + 0.20F</code><br>
        • <strong>K (Skill Match)</strong>: 1.0 if asset matches required facility/skill; 0 otherwise.<br>
        • <strong>A (Availability)</strong>: Free Hours / Total Capacity Hours.<br>
        • <strong>F (Workload Fairness)</strong>: 1 - (Active Load / Total Capacity) to prevent burnout.<br>
        • <em>Weights can be adjusted live in System Settings!</em>`,
        chips: ['System Settings', 'Classroom Details', 'How to Allocate']
    },
    {
        keywords: ['allocate', 'assign', 'how to allocate', 'pending', 'task'],
        reply: `<strong>⚡ How to Allocate Tasks:</strong><br>
        1. Open the <strong>Dashboard</strong>.<br>
        2. In the <em>Task &amp; Request Queue</em>, find any pending request and click <strong>Allocate</strong>.<br>
        3. Or type your need into the <strong>Natural Language Allocator</strong> bar (e.g. <em>"I need Classroom B01 for 60 students"</em>) and click <em>Auto-Allocate</em>.<br>
        4. The engine locks candidate records via InnoDB row-level locking (<code class="font-mono text-xs">SELECT ... FOR UPDATE</code>) to commit without double-booking.`,
        chips: ['How to Release', 'Scoring Formula', 'NLP Auto-Allocate']
    },
    {
        keywords: ['release', 'free', 'cancel', 'deallocate', 'unassign'],
        reply: `<strong>🔄 How to Release Resources:</strong><br>
        • <strong>From Dashboard</strong>: Click the <em>Release</em> button next to any assignment in the Active Allocations table.<br>
        • <strong>From Calendar</strong>: Click any booked timetable slot and select <em>Release Resource Slot</em>.<br>
        • The system immediately restores the asset's available hours!`,
        chips: ['Scheduling Calendar', 'Classroom Details']
    },
    {
        keywords: ['calendar', 'timetable', 'schedule', 'slot', 'time'],
        reply: `<strong>📅 Scheduling Matrix:</strong><br>
        The Calendar page displays a 9-slot hourly timetable (08:00 to 17:00) with conflict-free double-booking prevention. You can filter by date or sector (Classrooms, Staff, IT Labs, Fleet, Catering).`,
        chips: ['Classroom Details', 'How to Release']
    },
    {
        keywords: ['contact', 'phone', 'email', 'support', 'helpdesk'],
        reply: `<strong>📞 Official RTRAE Systems Support:</strong><br>
        • <strong>Helpline</strong>: <a href="tel:7048700224" class="fw-bold" style="color:#315F43;">+91 7048700224</a><br>
        • <strong>Email</strong>: <a href="mailto:25amtics@gmail.com" class="fw-bold" style="color:#315F43;">25amtics@gmail.com</a><br>
        • <strong>Campus</strong>: Academic Operations &amp; Resource Center.`,
        chips: ['Classroom Details', 'Scoring Formula']
    }
];

/* Robust Chatbot Toggle & Close Functions (Guaranteed to Work) */
function openGlobalAiChat(e) {
    if (e) { e.preventDefault(); e.stopPropagation(); }
    const box = document.getElementById('aiChatBox');
    const btn = document.getElementById('aiToggleBtn');
    if (box) {
        box.style.setProperty('display', 'flex', 'important');
        box.classList.remove('d-none');
        const input = document.getElementById('aiChatInput');
        if (input) input.focus();

        const messages = document.getElementById('aiChatMessages');
        if (messages && messages.children.length === 0) {
            renderAiBotReply(`Hello! I am your <strong>RTRAE Assistant</strong>. I have complete knowledge of this platform and webpage. Ask me anything about classrooms (B01–B13), computing labs, the suitability formula, or how to allocate tasks!`,
            ['Classroom Details', 'Computer Lab Specs', 'Scoring Formula', 'How to Allocate', 'Contact Support']);
        }
    }
    if (btn) {
        btn.style.setProperty('display', 'none', 'important');
        btn.classList.add('d-none');
    }
}

function closeGlobalAiChat(e) {
    if (e) { e.preventDefault(); e.stopPropagation(); }
    const box = document.getElementById('aiChatBox');
    const btn = document.getElementById('aiToggleBtn');
    if (box) {
        box.style.setProperty('display', 'none', 'important');
        box.classList.add('d-none');
    }
    if (btn) {
        btn.style.setProperty('display', 'flex', 'important');
        btn.classList.remove('d-none');
    }
}

function toggleGlobalAiChat(e) {
    const box = document.getElementById('aiChatBox');
    if (box && (box.style.display === 'flex' || (!box.classList.contains('d-none') && box.style.display !== 'none'))) {
        closeGlobalAiChat(e);
    } else {
        openGlobalAiChat(e);
    }
}

// Close on Escape key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        const box = document.getElementById('aiChatBox');
        if (box && box.style.display === 'flex') {
            closeGlobalAiChat(e);
        }
    }
});

function renderAiBotReply(html, chips = []) {
    const box = document.getElementById('aiChatMessages');
    if (!box) return;

    const wrap = document.createElement('div');
    wrap.className = 'mb-3 text-start';

    let chipsHtml = '';
    if (chips && chips.length > 0) {
        chipsHtml = `<div class="mt-2 pt-2 border-top border-secondary border-opacity-25">` + 
            chips.map(c => `<button type="button" class="ai-chip" onclick="askAiQuestion('${c.replace(/'/g, "\\'")}')">${c}</button>`).join('') + 
            `</div>`;
    }

    wrap.innerHTML = `
        <div class="d-flex align-items-start gap-2">
            <div class="rounded-circle p-1 d-flex align-items-center justify-content-center text-white" style="width:26px;height:26px;background:#315F43;font-size:0.75rem;flex-shrink:0;">
                <i class="bi bi-robot"></i>
            </div>
            <div class="ai-bubble ai-bubble-bot">
                ${html}
                ${chipsHtml}
            </div>
        </div>
    `;
    box.appendChild(wrap);
    box.scrollTop = box.scrollHeight;
}

function renderAiUserMessage(text) {
    const box = document.getElementById('aiChatMessages');
    if (!box) return;

    const wrap = document.createElement('div');
    wrap.className = 'mb-3 text-end';
    wrap.innerHTML = `
        <div class="ai-bubble ai-bubble-user d-inline-block">
            ${escapeHtmlText(text)}
        </div>
    `;
    box.appendChild(wrap);
    box.scrollTop = box.scrollHeight;
}

function escapeHtmlText(str) {
    return String(str ?? '').replace(/[&<>"']/g, c => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    }[c]));
}

function askAiQuestion(qText) {
    const input = document.getElementById('aiChatInput');
    if (input) input.value = qText;
    sendGlobalAiChat();
}

function sendGlobalAiChat() {
    const input = document.getElementById('aiChatInput');
    if (!input) return;
    const query = input.value.trim();
    if (!query) return;

    renderAiUserMessage(query);
    input.value = '';

    const box = document.getElementById('aiChatMessages');
    const typingId = 'ai-typing-' + Date.now();
    const typingWrap = document.createElement('div');
    typingWrap.id = typingId;
    typingWrap.className = 'mb-2 text-start text-muted';
    typingWrap.innerHTML = `<small><i class="bi bi-three-dots me-1"></i>Consulting webpage knowledge base…</small>`;
    box.appendChild(typingWrap);
    box.scrollTop = box.scrollHeight;

    setTimeout(() => {
        const el = document.getElementById(typingId);
        if (el) el.remove();

        const lower = query.toLowerCase();
        let matched = null;

        for (const item of RTRAE_KB) {
            if (item.keywords.some(k => lower.includes(k))) {
                matched = item;
                break;
            }
        }

        if (matched) {
            renderAiBotReply(matched.reply, matched.chips || []);
        } else {
            renderAiBotReply(`I understand your question about "<strong>${escapeHtmlText(query)}</strong>". Here are some core topics I can help with:`, 
            ['Classroom Details', 'Computer Lab Specs', 'Scoring Formula', 'How to Allocate', 'Contact Support']);
        }
    }, 250);
}

document.addEventListener('DOMContentLoaded', () => {
    const chatInput = document.getElementById('aiChatInput');
    if (chatInput) {
        chatInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                sendGlobalAiChat();
            }
        });
    }

    const closeBtn = document.getElementById('aiChatCloseBtn');
    if (closeBtn) {
        closeBtn.addEventListener('click', closeGlobalAiChat);
    }
    const minBtn = document.getElementById('aiChatMinBtn');
    if (minBtn) {
        minBtn.addEventListener('click', closeGlobalAiChat);
    }
});

// Click outside to close ("cut") the chatbot
document.addEventListener('click', (e) => {
    const box = document.getElementById('aiChatBox');
    const btn = document.getElementById('aiToggleBtn');
    if (box && box.style.display === 'flex') {
        if (!box.contains(e.target) && !btn.contains(e.target)) {
            closeGlobalAiChat(e);
        }
    }
});

window.closeAiChat = closeGlobalAiChat;
window.closeGlobalAiChat = closeGlobalAiChat;
window.openAiChat = openGlobalAiChat;
window.openGlobalAiChat = openGlobalAiChat;
</script>
