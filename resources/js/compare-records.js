import $ from 'jquery';

let currentPatient = null;
let currentPatientRecords = [];
let searchTimeout = null;
let comparisonMode = false;
let selectedRecordForComparison = null;
let controller = null;

/* -------------------------
   UTILITIES
   ------------------------- */

function fmt(dt) {
    try {
        return new Date(dt).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    } catch {
        return dt;
    }
}

function formatDateShort(dt) {
    try {
        return new Date(dt).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    } catch {
        return dt;
    }
}

/* -------------------------
   PATIENT SEARCH (DEBOUNCED)
   ------------------------- */

async function searchPatients(query) {
    if (!query || query.trim().length < 2) {
        $('#patientResultsArea').addClass('hidden');
        $('#patientResultsList').html('');
        return;
    }

    const spinner = $('#searchSpinner');
    spinner.removeClass('hidden');

    try {
        const res = await fetch(`/api/getPatient/search?q=${encodeURIComponent(query.trim())}`);
        if (!res.ok) throw new Error('Search failed');
        
        const patients = await res.json();
        spinner.addClass('hidden');

        if (!patients || patients.length === 0) {
            $('#patientResultsList').html(
                '<div class="text-sm text-gray-400 py-2">No patients found</div>'
            );
            $('#patientResultsArea').removeClass('hidden');
            return;
        }

        renderPatientResults(patients);

    } catch (error) {
        console.error('Search error:', error);
        spinner.addClass('hidden');
        $('#patientResultsList').html(
            '<div class="text-sm text-red-500 py-2">Error searching patients</div>'
        );
        $('#patientResultsArea').removeClass('hidden');
    }
}

function renderPatientResults(patients) {
    if (!patients || patients.length === 0) {
        $('#patientResultsList').html(
            '<div class="text-sm text-gray-400 py-2">No patients found</div>'
        );
        $('#patientResultsArea').removeClass('hidden');
        return;
    }

    // Clear and render with animation
    $('#patientResultsList').html('');

    // Render each card with staggered animation
    patients.forEach((p, index) => {
        const $card = $(`
            <div class="patient-result-card" data-patient-id="${p.id}" style="opacity: 0; transform: translateX(-20px);">
                <div class="font-medium text-sm text-gray-900">${p.full_name}</div>
                <div class="text-xs text-gray-500 mt-1">${p.sex} • ${p.age} yrs • ${p.unit || 'N/A'}</div>
            </div>
        `);

        $('#patientResultsList').append($card);

        // Animate in with delay
        setTimeout(() => {
            $card.css({
                'transition': 'all 0.3s ease-out',
                'opacity': '1',
                'transform': 'translateX(0)'
            });
        }, index * 30); // Stagger animation
    });

    $('#patientResultsArea').removeClass('hidden');

    // Attach click handlers
    $('.patient-result-card').off('click').on('click', function() {
        const patientId = $(this).data('patient-id');
        const patient = patients.find(p => p.id === patientId);
        if (patient) {
            selectPatient(patient);
        }
    });
}

// Populate comparison dropdown (exclude the first selected record)
function populateCompareSelect(excludeId) {
    const $select = $('#compareRecordSelect');
    if (!$select.length) return;
    
    $select.empty();
    $select.append(`<option value="">Select record…</option>`);
    
    (currentPatientRecords || []).forEach(r => {
        if (r.id === excludeId) return;
        $select.append(`
            <option value="${r.id}">
                Record #${r.id} — ${formatDateShort(r.created_at)}
            </option>
        `);
    });
    
    $select.off('change').on('change', function() {
        const id = Number($(this).val());
        if (!id) return;
        const rec = (currentPatientRecords || []).find(x => x.id === id);
        if (rec) selectRecordForComparison(rec);
    });
}

/* -------------------------
   PATIENT SELECTION
   ------------------------- */

async function selectPatient(patient) {
    currentPatient = patient;

    // Update UI
    $('#selectedPatientName').text(patient.full_name);
    $('#selectedPatientDetails').text(`${patient.sex} • ${patient.age} yrs • ${patient.unit || 'N/A'}`);
    $('#selectedPatientInfo').removeClass('hidden');

    // Highlight selected patient card
    $('.patient-result-card').removeClass('selected');
    $(`.patient-result-card[data-patient-id="${patient.id}"]`).addClass('selected');

    // Hide search results
    $('#patientResultsArea').addClass('hidden');

    // Clear search input
    $('#record-search input').val(patient.full_name);

    // Load patient records
    await loadPatientRecords(patient.id);
}

function clearPatient() {
    currentPatient = null;
    currentPatientRecords = [];
    
    $('#selectedPatientInfo').addClass('hidden');
    $('#recordsCarouselArea #emptyState').removeClass('hidden');
    $('#recordsContainer').html('');
    $('#record-search input').val('');
    $('.patient-result-card').removeClass('selected');
}

/* -------------------------
   LOAD PATIENT RECORDS
   ------------------------- */

async function loadPatientRecords(patientId) {
    try {
        $('#recordsContainer').html(`
            <div class="flex items-center justify-center w-full py-12">
                <div class="text-center">
                    <div class="w-8 h-8 border-4 border-gray-200 border-t-blue-600 rounded-full animate-spin mx-auto mb-3"></div>
                    <div class="text-sm text-gray-500">Loading records...</div>
                </div>
            </div>
        `);

        const res = await fetch(`/api/patients/${patientId}/records/compare`);
        if (!res.ok) throw new Error('Failed to load records');

        const { patient, records } = await res.json();
        currentPatientRecords = records || [];

        if (!currentPatientRecords || currentPatientRecords.length === 0) {
            $('#recordsContainer').html(`
                <div class="flex items-center justify-center w-full py-12">
                    <div class="text-center text-gray-500">
                        <div class="text-lg font-medium mb-2">No records found</div>
                        <div class="text-sm">This patient has no records yet</div>
                    </div>
                </div>
            `);
            $('#recordsCarouselArea #emptyState').addClass('hidden');
            return;
        }

        renderRecordCards(currentPatientRecords);
        $('#recordsCarouselArea #emptyState').addClass('hidden');

    } catch (error) {
        console.error('Load records error:', error);
        $('#recordsContainer').html(`
            <div class="flex items-center justify-center w-full py-12">
                <div class="text-center text-red-500">
                    <div class="text-lg font-medium mb-2">Error loading records</div>
                    <div class="text-sm">Please try again</div>
                </div>
            </div>
        `);
    }
}

/* -------------------------
   RENDER RECORD CARDS
   ------------------------- */

function renderRecordCards(records) {
    if (!records || records.length === 0) {
        $('#recordsContainer').html('');
        return;
    }

    $('#recordsContainer').html(
        records.map(record => createRecordCard(record)).join('')
    );

    // Attach click handlers
    $('.record-card').off('click').on('click', function() {
        const recordId = $(this).data('record-id');
        const record = records.find(r => r.id === recordId);
        if (!record) return;
        
        if (comparisonMode) {
            // In comparison mode, select record for comparison
            selectRecordForComparison(record);
        } else {
            // Normal mode, open modal
            openRecordModal(record);
        }
    });
}

function createRecordCard(record) {
    const hasReport = record.generated_report && record.generated_report.generated_text;
    const reportPreview = hasReport
        ? record.generated_report.generated_text.substring(0, 200).replace(/\n/g, ' ') + '...'
        : 'No report available';

    return `
        <div class="record-card" data-record-id="${record.id}">
            <div class="record-card-header">
                <div class="flex items-center justify-between mb-2">
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        Record #${record.id}
                    </div>
                    ${hasReport ? `
                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                            <i class="fa-solid fa-check-circle mr-1"></i>Report Available
                        </span>
                    ` : `
                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">
                            <i class="fa-solid fa-clock mr-1"></i>Pending
                        </span>
                    `}
                </div>
                <div class="text-sm font-semibold text-gray-900">
                    ${formatDateShort(record.created_at)}
                </div>
            </div>

            <div class="record-card-body">
                <!-- Metrics -->
                <div class="space-y-2 mb-4">
                    ${record.cholesterol ? `
                        <div class="record-metric">
                            <span class="record-metric-label">Cholesterol</span>
                            <span class="record-metric-value">${record.cholesterol} mg/dL</span>
                        </div>
                    ` : ''}
                    ${record.hdl_cholesterol ? `
                        <div class="record-metric">
                            <span class="record-metric-label">HDL</span>
                            <span class="record-metric-value">${record.hdl_cholesterol} mg/dL</span>
                        </div>
                    ` : ''}
                    ${record.systolic_bp ? `
                        <div class="record-metric">
                            <span class="record-metric-label">Systolic BP</span>
                            <span class="record-metric-value">${record.systolic_bp} mmHg</span>
                        </div>
                    ` : ''}
                    ${record.fbs ? `
                        <div class="record-metric">
                            <span class="record-metric-label">FBS</span>
                            <span class="record-metric-value">${record.fbs} mg/dL</span>
                        </div>
                    ` : ''}
                    ${record.hba1c ? `
                        <div class="record-metric">
                            <span class="record-metric-label">HbA1c</span>
                            <span class="record-metric-value">${record.hba1c}%</span>
                        </div>
                    ` : ''}
                </div>

                <!-- Risk Factors -->
                <div class="mb-4">
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">
                        Risk Factors
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="risk-badge ${record.hypertension ? 'active' : 'inactive'}">
                            <i class="fa-solid ${record.hypertension ? 'fa-check' : 'fa-xmark'}"></i>
                            Hypertension
                        </span>
                        <span class="risk-badge ${record.diabetes ? 'active' : 'inactive'}">
                            <i class="fa-solid ${record.diabetes ? 'fa-check' : 'fa-xmark'}"></i>
                            Diabetes
                        </span>
                        <span class="risk-badge ${record.smoking ? 'active' : 'inactive'}">
                            <i class="fa-solid ${record.smoking ? 'fa-check' : 'fa-xmark'}"></i>
                            Smoking
                        </span>
                    </div>
                </div>

                <!-- Report Preview -->
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">
                        Report Preview
                    </div>
                    <div class="text-xs text-gray-600 leading-relaxed line-clamp-4">
                        ${reportPreview}
                    </div>
                </div>
            </div>

            <div class="record-card-footer">
                <button class="w-full hhi-btn hhi-btn-view text-xs">
                    <i class="fa-solid fa-eye mr-1"></i>
                    View Full Report
                </button>
            </div>
        </div>
    `;
}

/* -------------------------
   RECORD MODAL
   ------------------------- */

function openRecordModal(record) {
    selectedRecordForComparison = record;
    comparisonMode = false;
    
    const hasReport = record.generated_report && record.generated_report.generated_text;
    
    $('#modalRecordTitle').text(`Record #${record.id}`);
    $('#modalRecordDate').text(`Created: ${fmt(record.created_at)}`);
    
    // Show compare button only if report is available
    if (hasReport) {
        $('#enterCompareModeBtn').removeClass('hidden');
    } else {
        $('#enterCompareModeBtn').addClass('hidden');
    }
    
    // Show single record view, hide comparison view
    $('#modalRecordContent').removeClass('hidden');
    $('#modalCompareContent').addClass('hidden');
    $('#modalContainer').removeClass('max-w-7xl').addClass('max-w-4xl').css({
        'max-height': '90vh',
        'margin-top': 'auto',
        'margin-bottom': 'auto'
    });
    $('#recordModal').removeClass('bg-black/20 pointer-events-none').addClass('bg-black/40');
    $('#modalContainer').css('pointer-events', 'auto');
    $('#recordsCarouselArea').css('z-index', 'auto');
    
    if (!hasReport) {
        $('#modalRecordContent').html(`
            <div class="text-center py-12 text-gray-500">
                <i class="fa-solid fa-file-circle-question text-4xl mb-4"></i>
                <div class="text-lg font-medium mb-2">No Report Available</div>
                <div class="text-sm">This record has not been evaluated yet</div>
            </div>
        `);
    } else {
        // Single column format - clean and readable
        const content = record.generated_report.generated_text
            .replace(/\n{3,}/g, '\n\n') // Clean up excessive line breaks
            .trim();
        
        $('#modalRecordContent').html(`
            <div class="prose prose-sm max-w-none">
                <div class="whitespace-pre-wrap text-gray-700 leading-relaxed">
                    ${content.replace(/\n/g, '<br>')}
                </div>
            </div>
        `);
    }
    
    $('#recordModal').removeClass('hidden');
}

function enterCompareMode() {
    if (!selectedRecordForComparison) return;
    
    comparisonMode = true;
    
    // Hide single record view, show comparison view
    $('#modalRecordContent').addClass('hidden');
    $('#modalCompareContent').removeClass('hidden');
    $('#enterCompareModeBtn').addClass('hidden');
    
    // Update modal container width for comparison
    $('#modalContainer').removeClass('max-w-4xl').addClass('max-w-7xl').css({
        'max-height': '90vh',
        'margin-top': 'auto',
        'margin-bottom': 'auto'
    });
    // Keep normal backdrop; selection will be via dropdown/select inside modal
    $('#recordModal').removeClass('bg-black/20 pointer-events-none').addClass('bg-black/40');
    $('#modalContainer').css('pointer-events', 'auto');
    $('#recordsCarouselArea').css('z-index', 'auto');
    
    // Load first record
    const record1 = selectedRecordForComparison;
    const hasReport1 = record1.generated_report && record1.generated_report.generated_text;
    
    $('#compareRecord1Title').text(`Record #${record1.id} - ${formatDateShort(record1.created_at)}`);
    
    if (!hasReport1) {
        $('#compareRecord1Content').html(`
            <div class="text-center py-12 text-gray-500">
                <i class="fa-solid fa-file-circle-question text-3xl mb-3"></i>
                <div class="text-sm font-medium mb-1">No Report Available</div>
                <div class="text-xs">This record has not been evaluated yet</div>
            </div>
        `);
    } else {
        const content1 = record1.generated_report.generated_text
            .replace(/\n{3,}/g, '\n\n')
            .trim();
        
        $('#compareRecord1Content').html(`
            <div class="whitespace-pre-wrap text-gray-700 leading-relaxed">
                ${content1.replace(/\n/g, '<br>')}
            </div>
        `);
    }
    
    // Reset second record
    $('#compareRecord2Title').text('Select Record');
    $('#compareRecord2Content').html(`
        <div class="text-center py-12 text-gray-400">
            <i class="fa-solid fa-hand-pointer text-3xl mb-3"></i>
            <div class="text-sm font-medium mb-1">Select a record to compare</div>
            <div class="text-xs">Use the dropdown or click a record card below</div>
        </div>
    `);
    
    // Populate select options from currentPatientRecords
    populateCompareSelect(record1.id);
    
    // Keep cards clickable (but no special overlay tricks)
    $('.record-card').addClass('cursor-pointer');
}

function exitCompareMode() {
    comparisonMode = false;
    
    // Show single record view, hide comparison view
    $('#modalRecordContent').removeClass('hidden');
    $('#modalCompareContent').addClass('hidden');
    
    $('#modalContainer').removeClass('max-w-7xl').addClass('max-w-4xl').css({
        'max-height': '90vh',
        'margin-top': 'auto',
        'margin-bottom': 'auto'
    });
    $('#recordModal').removeClass('bg-black/20 pointer-events-none').addClass('bg-black/40');
    $('#modalContainer').css('pointer-events', 'auto');
    $('.record-card').removeClass('cursor-pointer');
    
    // Show compare button again if record has report
    if (selectedRecordForComparison?.generated_report?.generated_text) {
        $('#enterCompareModeBtn').removeClass('hidden');
    }
}

function selectRecordForComparison(record) {
    if (!comparisonMode) return;
    
    const hasReport2 = record.generated_report && record.generated_report.generated_text;
    
    $('#compareRecord2Title').text(`Record #${record.id} - ${formatDateShort(record.created_at)}`);
    
    if (!hasReport2) {
        $('#compareRecord2Content').html(`
            <div class="text-center py-12 text-gray-500">
                <i class="fa-solid fa-file-circle-question text-3xl mb-3"></i>
                <div class="text-sm font-medium mb-1">No Report Available</div>
                <div class="text-xs">This record has not been evaluated yet</div>
            </div>
        `);
    } else {
        const content2 = record.generated_report.generated_text
            .replace(/\n{3,}/g, '\n\n')
            .trim();
        
        $('#compareRecord2Content').html(`
            <div class="whitespace-pre-wrap text-gray-700 leading-relaxed">
                ${content2.replace(/\n/g, '<br>')}
            </div>
        `);
    }
    
    // Keep highlight on cards but remove the pulsing effect
    $('.record-card').removeClass('ring-offset-2');
}

function closeRecordModal() {
    comparisonMode = false;
    selectedRecordForComparison = null;
    
    // Reset modal state
    exitCompareMode();
    
    $('#recordModal').addClass('hidden');
}

/* -------------------------
   EVENT HANDLERS
   ------------------------- */

// Search with debounce
const $searchInput = $('#record-search input').length
    ? $('#record-search input')
    : $('#record-search');


    
    if ($searchInput.length) {
        $searchInput.on("input", function () {
            const query = $(this).val().trim();
    
            // Clear pending debounce
            clearTimeout(searchTimeout);
    
            // Abort in-flight request immediately
            if (controller) {
                controller.abort();
                controller = null;
            }
    
            // Hide results if query too short
            if (query.length < 2) {
                $("#patientResultsArea").addClass("hidden");
                return;
            }
    
            // Debounce (REAL debounce)
            searchTimeout = setTimeout(() => {
                searchPatients(query);
            }, 350); // 👈 300–400ms is ideal
        });
    }
    

// Reset button
const $resetBtn = $('#reset-filters');
if ($resetBtn.length) {
    $resetBtn.on('click', function(e) {
        e.preventDefault();
        const mode = $(this).attr('data-mode') || 'search';
        
        if (mode === 'reset') {
            window.location.reload();
        }
        // In search mode, clicking does nothing (search happens on input)
    });
}

// Clear patient button
$('#clearPatientBtn').on('click', clearPatient);

// Modal close
$('#closeRecordModal').on('click', closeRecordModal);
$('#recordModal').on('click', function(e) {
    // Only close on outside click if NOT in comparison mode
    if (e.target === this && !comparisonMode) {
        closeRecordModal();
    }
});

// Comparison mode buttons
$('#enterCompareModeBtn').on('click', enterCompareMode);
$('#exitCompareModeBtn').on('click', exitCompareMode);

// Keyboard shortcuts
$(document).on('keydown', function(e) {
    if (e.key === 'Escape') {
        if (comparisonMode) {
            exitCompareMode();
        } else {
            closeRecordModal();
        }
    }
});

/* -------------------------
   INIT
   ------------------------- */

$(document).ready(() => {
    // Check URL params for direct patient/record load
    const urlParams = new URLSearchParams(window.location.search);
    const patientId = urlParams.get('patient') || urlParams.get('special');
    const recordId = urlParams.get('record');
    
    if (recordId) {
        // Load specific record
        fetch(`/api/records/${recordId}/compare`)
            .then(res => res.json())
            .then(({ patient, record, records }) => {
                if (patient) {
                    selectPatient({
                        id: patient.id,
                        full_name: `${patient.first_name} ${patient.last_name}`,
                        sex: patient.sex,
                        age: patient.age,
                        unit: patient.unit
                    });
                }
            })
            .catch(console.error);
    } else if (patientId) {
        // Search for patient by ID
        fetch(`/api/getPatient/search?q=${patientId}`)
            .then(res => res.json())
            .then(patients => {
                if (patients && patients.length > 0) {
                    const patient = patients.find(p => p.id == patientId) || patients[0];
                    if (patient) {
                        selectPatient(patient);
                    }
                }
            })
            .catch(console.error);
    }
});
