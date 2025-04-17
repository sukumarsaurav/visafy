<?php
$page_title = "Assessment Tools | CANEXT Immigration";
include('includes/header.php');
?>
<?php
// Define page title and include header
$pageTitle = "Immigration Eligibility Checker";
include_once("includes/header.php");
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Immigration Eligibility Checker</h3>
                </div>
                <div class="card-body">
                    <!-- Primary Category Selection -->
                    <div id="primary-category" class="eligibility-section">
                        <h4>Step 1: Select your primary immigration category</h4>
                        <div class="form-group mt-3">
                            <select id="primaryCategory" class="form-control">
                                <option value="">-- Select Category --</option>
                                <option value="study">Study</option>
                                <option value="work">Work</option>
                                <option value="permanent">Permanent Residence</option>
                                <option value="invest">Invest/Business</option>
                                <option value="visitor">Visitor</option>
                            </select>
                        </div>
                    </div>

                    <!-- Dynamic Question Container -->
                    <div id="question-container" class="eligibility-section mt-4" style="display: none;">
                        <div id="question-header" class="mb-3"></div>
                        <div id="question-content"></div>
                        <div id="navigation-buttons" class="mt-4">
                            <button id="prev-button" class="btn btn-secondary" style="display: none;">Previous</button>
                            <button id="next-button" class="btn btn-primary" style="display: none;">Next</button>
                        </div>
                    </div>

                    <!-- Results Container -->
                    <div id="result-container" class="eligibility-section mt-4" style="display: none;">
                        <div class="alert" id="result-alert">
                            <h4 id="result-title"></h4>
                            <p id="result-message"></p>
                            <div id="result-links"></div>
                        </div>
                        <button id="restart-button" class="btn btn-primary mt-3">Start New Assessment</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SDS Country List Modal -->
<div class="modal fade" id="sdsCountriesModal" tabindex="-1" role="dialog" aria-labelledby="sdsCountriesModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sdsCountriesModalLabel">SDS Eligible Countries</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>The Student Direct Stream (SDS) is available to legal residents of:</p>
                <ul>
                    <li>Antigua and Barbuda</li>
                    <li>Brazil</li>
                    <li>China</li>
                    <li>Colombia</li>
                    <li>Costa Rica</li>
                    <li>India</li>
                    <li>Morocco</li>
                    <li>Pakistan</li>
                    <li>Peru</li>
                    <li>Philippines</li>
                    <li>Senegal</li>
                    <li>Saint Vincent and the Grenadines</li>
                    <li>Trinidad and Tobago</li>
                    <li>Vietnam</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Custom CSS -->
<style>
    .eligibility-section {
        padding: 15px;
    }
    .answer-option {
        margin: 10px 0;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        cursor: pointer;
        transition: all 0.3s;
    }
    .answer-option:hover {
        background-color: #f8f9fa;
    }
    .answer-option.selected {
        background-color: #d4edda;
        border-color: #c3e6cb;
    }
    .alert-eligible {
        background-color: #d4edda;
        border-color: #c3e6cb;
        color: #155724;
    }
    .alert-not-eligible {
        background-color: #f8d7da;
        border-color: #f5c6cb;
        color: #721c24;
    }
    .alert-info {
        background-color: #d1ecf1;
        border-color: #bee5eb;
        color: #0c5460;
    }
    .consultant-link {
        margin-top: 10px;
    }
</style>

<!-- Include jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Include custom eligibility JavaScript -->
<script src="assets/js/eligibility.js"></script>

<?php
// Include footer
include_once("includes/footer.php");
?>