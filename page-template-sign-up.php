<?php

/*
* Template Name: Register Page
*/

get_header(); ?>


<main role="main" class="site-main page-template-register">

  <div class="content-block">
    <div class="content-max">

      <div class="page-template-register__content">

        <!-- Alpine multistep Registration -->
        <div x-data="registerForm()" x-init="init()" class="page-template-register__form-container">



          <!-- STEP 1: Basic Info -->
          <template x-if="step === 1">
            <form @submit.prevent="next()">
              <p class="page-template-register__form-tagline">//ACCOUNT</p>
              <h2 class="page-template-register__form-title">BASIC INFORMATION</h2>
              <div class="page-template-register__form-field-wrapper">
                <label>First Name *
                  <input class="page-template-register__input-field" type="text" name="first_name" placeholder="Enter Name" x-model="form.first_name" required>
                </label>
                <label>Last Name *
                  <input class="page-template-register__input-field" type="text" name="last_name" placeholder="Enter Last Name" x-model="form.last_name" required>
                </label>
                <label>Phone Number
                  <input class="page-template-register__input-field" type="tel" name="phone_number" placeholder="Enter Phone Number" x-model="form.phone_number">
                </label>
                <label>Contact Email *
                  <input class="page-template-register__input-field" type="email" name="contact_email" placeholder="Enter Email" x-model="form.contact_email" required>
                </label>
                <div>
                  <label class="page-template-register__form-checkbox">
                    <input type="checkbox" name="keep_private" x-model="form.keep_private">
                    Would you like to keep your contact details private, and only receive messages via website?
                  </label>
                  <label class="page-template-register__form-checkbox">
                    <input type="checkbox" name="opt_out_marketing" x-model="form.opt_out_marketing">
                    Please opt me out of any availability updates, news & marketing.
                  </label>
                </div>
              </div>

              <div class="page-template-register__form-button-wrapper">

                <button class="fdry-form-btn next">Next</button>
              </div>
              <span class="page-template-register__form-login-link">Already have an account? <a href="<?php echo home_url('/login'); ?>">Sign in</a></span>
            </form>
          </template>

          <!-- STEP 2: Professional Info -->
          <template x-if="step === 2">
            <form @submit.prevent="next()">
              <p class="page-template-register__form-tagline">//ACCOUNT</p>
              <h2 class="page-template-register__form-title">Professional info</h2>
              <div class="page-template-register__form-field-wrapper">
                <label>Department *
                  <div class="select">
                    <select class="page-template-register__input-field" name="department" x-model="form.department" required>
                      <option value="" disabled selected>Enter department</option>
                      <template x-for="t in tax.departments" :key="t.id">
                        <option :value="t.id" x-text="t.name" :selected="form.department == t.id"></option>
                      </template>
                    </select>
                    <i><?php get_template_part('svg-template/svg-chevron-down') ?></i>
                  </div>
                </label>
                <label>Role title *
                  <div class="select">
                    <select class="page-template-register__input-field" name="role_title" x-model="form.role_title" required>
                      <option value="" disabled selected>Enter title</option>
                      <template x-for="t in tax.roles" :key="t.id">
                        <option :value="t.id" x-text="t.name" :selected="form.role_title == t.id"></option>
                      </template>
                    </select>
                    <i><?php get_template_part('svg-template/svg-chevron-down') ?></i>
                  </div>
                </label>
                <label>IMDB link *
                  <input class="page-template-register__input-field" type="url" name="imdb_link" x-model="form.imdb_link" placeholder="Enter IMDB link" required>
                </label>
              </div>

              <div class="page-template-register__form-button-wrapper">
                <button type="button" class="fdry-form-btn back-btn" @click="prev()">Back</button>
                <button class="fdry-form-btn">Next</button>
              </div>
            </form>
          </template>

          <!-- STEP 3: Location & Availability -->
          <template x-if="step === 3">
            <form @submit.prevent="next()">
              <p class="page-template-register__form-tagline">//ACCOUNT</p>
              <h2 class="page-template-register__form-title">Location & Availability</h2>
              <div class="page-template-register__form-field-wrapper">

                <label>Enter your post code * (Used for API purposes)
                  <input class="page-template-register__input-field" type="text" name="postcode" placeholder="Enter post code" x-model="form.postcode" required>
                  <span class="page-template-register__form-description">Will <strong>not</strong> be shown on your profile</span>
                </label>
                <label>Select the Areas or Regions you can work in
                  <div class="select">
                    <select class="page-template-register__input-field" name="area_region" x-model="form.area_region">
                      <option value="">Select a region</option>
                      <option value="East Midlands">East Midlands</option>
                      <option value="East of England">East of England</option>
                      <option value="London">London</option>
                      <option value="North East">North East</option>
                      <option value="North West">North West</option>
                      <option value="South East">South East</option>
                      <option value="South West">South West</option>
                      <option value="West Midlands">West Midlands</option>
                      <option value="Yorkshire and the Humber">Yorkshire and the Humber</option>
                      <option value="Northern Ireland">Northern Ireland</option>
                      <option value="Scotland">Scotland</option>
                      <option value="Wales">Wales</option>
                    </select>
                    <i><?php get_template_part('svg-template/svg-chevron-down') ?></i>
                  </div>
                </label>

                <label>Current availability status
                  <div class="select">
                    <select class="page-template-register__input-field" name="availability_status" x-model="form.availability_status" required>
                      <option value="available">Available</option>
                      <option value="booked">Booked</option>
                      <option value="penciled">Penciled</option>
                    </select>
                    <i><?php get_template_part('svg-template/svg-chevron-down') ?></i>
                  </div>
                </label>

                <!-- Conditional dates -->
                <!-- For PENCILED: show "from" (required) and "until" (optional) -->
                <template x-if="form.availability_status === 'penciled'">
                  <label>Available from (YYYY-MM-DD) *
                    <input class="page-template-register__input-field" type="date" name="available_from" x-model="form.available_from" :min="new Date().toISOString().split('T')[0]" @change="validateDates()" required>
                  </label>
                </template>

                <!-- For BOOKED: only show "until" (optional, for "booked until X") -->
                <template x-if="form.availability_status === 'booked'">
                  <label>Booked until (optional, YYYY-MM-DD)
                    <input class="page-template-register__input-field" type="date" name="available_until" x-model="form.available_until" :min="new Date().toISOString().split('T')[0]" @change="validateDates()">
                  </label>
                </template>

                <!-- For PENCILED: show "until" (optional) -->
                <template x-if="form.availability_status === 'penciled'">
                  <label>Available until (optional, YYYY-MM-DD)
                    <input class="page-template-register__input-field" type="date" name="available_until" x-model="form.available_until" :min="form.available_from || new Date().toISOString().split('T')[0]" @change="validateDates()">
                  </label>
                </template>

                <!-- For AVAILABLE: optionally show both dates -->
                <template x-if="form.availability_status === 'available'">
                  <div>
                    <label>Available from (optional, YYYY-MM-DD)
                      <input class="page-template-register__input-field" type="date" name="available_from" x-model="form.available_from" :min="new Date().toISOString().split('T')[0]" @change="validateDates()">
                    </label>
                    <label>Available until (optional, YYYY-MM-DD)
                      <input class="page-template-register__input-field" type="date" name="available_until" x-model="form.available_until" :min="form.available_from || new Date().toISOString().split('T')[0]" @change="validateDates()">
                    </label>
                  </div>
                </template>
              </div>

              <div class="page-template-register__form-button-wrapper">
                <button type="button" class="fdry-form-btn back-btn" @click="prev()">Back</button>
                <button class="fdry-form-btn">Next</button>
              </div>
            </form>
          </template>

          <!-- STEP 4: Work Experience -->
          <template x-if="step === 4">
            <form @submit.prevent="next()">
              <p class="page-template-register__form-tagline">//ACCOUNT</p>
              <h2 class="page-template-register__form-title no-mb">Work experience</h2>
              <p class="page-template-register__form-subtitle">MUST INCLUDE AT LEAST 3 PRODUCTIONS LISTED</p>

              <template x-for="(exp, i) in form.work_experience" :key="i">
                <div class="page-template-register__form-field-wrapper role" :class="i === 0 ? 'first' : ''">
                  <label>Role title *
                    <input class="page-template-register__input-field" type="text" :name="'we_role_title_' + i" x-model="exp.we_role_title" required>
                  </label>
                  <label>Production name *
                    <input class="page-template-register__input-field" type="text" :name="'we_production_name_' + i" x-model="exp.we_production_name" required>
                  </label>
                  <label>Date *
                    <input class="page-template-register__input-field" type="number" :name="'we_date_' + i" x-model="exp.we_date" min="1970" max="2100" placeholder="YYYY" required>
                  </label>
                  <div class="">
                    <button type="button" class="fdry-form-btn remove-btn blue" @click="removeExp(i)" x-show="form.work_experience.length > 3"><i><?php get_template_part('svg-template/svg-trash') ?></i></button>
                  </div>
                </div>
              </template>

              <div class="page-template-register__form-button-wrapper">
                <button type="button" class="fdry-form-btn back-btn" @click="prev()">Back</button>
                <div class="space-x-2">
                  <button type="button" class="fdry-form-btn add-btn green" @click="addExp()">+ Add another</button>
                  <button class="fdry-form-btn">Next</button>
                </div>
              </div>
            </form>
          </template>

          <!-- STEP 5: Profile Media -->
          <template x-if="step === 5">
            <form @submit.prevent="next()">
              <p class="page-template-register__form-tagline">//ACCOUNT</p>
              <h2 class="page-template-register__form-title">Profile media</h2>
              <div class="page-template-register__form-field-wrapper">
                <p class="page-template-register__form-description">Add a profile picture (Optional)</p>
                <div class="file-upload-wrapper">
                  <div class="file-upload-area"
                    @click="$refs.fileInput.click()"
                    @dragover.prevent="$event.target.classList.add('drag-over')"
                    @dragleave.prevent="$event.target.classList.remove('drag-over')"
                    @drop.prevent="handleFileDrop($event)">
                    <div class="upload-icon">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                        <polyline points="17,8 12,3 7,8" />
                        <line x1="12" y1="3" x2="12" y2="15" />
                      </svg>
                    </div>
                    <p class="upload-text" x-show="!file">Click to upload or drag and drop</p>
                    <p class="file-name" x-show="file" x-text="file?.name"></p>
                  </div>
                  <input type="file"
                    x-ref="fileInput"
                    id="profile-picture"
                    name="profile_picture"
                    accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                    @change="onFile($event)"
                    style="display: none;">
                </div>
              </div>

              <div class="page-template-register__form-button-wrapper">
                <button type="button" class="fdry-form-btn back-btn" @click="prev()">Back</button>
                <button class="fdry-form-btn">Next</button>
              </div>
            </form>
          </template>

          <!-- STEP 6: Sign-in details -->
          <template x-if="step === 6">
            <form @submit.prevent="submit()">
              <p class="page-template-register__form-tagline">//ACCOUNT</p>
              <h2 class="page-template-register__form-title">Sign-in details</h2>
              <div class="page-template-register__form-field-wrapper">
                <label>Account email (for login) *
                  <input class="page-template-register__input-field" type="email" name="email" x-model="form.email" required>
                </label>
                <label>Password *
                  <input class="page-template-register__input-field" type="password" name="password" x-model="form.password" minlength="8" required>
                </label>
              </div>

              <!-- Optional: place your CAPTCHA widget here and set form.captcha -->

              <div class="page-template-register__form-button-wrapper">
                <button type="button" class="fdry-form-btn back-btn" @click="prev()">Back</button>
                <button class="fdry-form-btn" :disabled="loading">
                  <span x-show="!loading">Create account</span>
                  <span x-show="loading">Submitting…</span>
                </button>
              </div>
            </form>
          </template>

          <!-- Messages -->
          <p class="page-template-register__form-error" x-text="error" x-show="error"></p>
          <p class="page-template-register__form-success" x-text="success" x-show="success"></p>

        </div>




      </div>

    </div>

  </div>

</main>

<?php get_footer(); ?>

<script>
  /**
   * TODO => MOVE IT TO SINGLE OR MULTIPLE JS FILE AND CALL IT FROM THERE
   * Alpine.js Multi-Step Registration Form
   * 
   * Handles a 6-step user registration process with validation,
   * file uploads, and API integration for creating user profiles.
   * 
   * Steps:
   * 1. Basic Information (name, email, phone)
   * 2. Professional Info (department, role, IMDB)
   * 3. Location & Availability (postcode, region, dates)
   * 4. Work Experience (minimum 3 productions)
   * 5. Profile Media (optional profile picture)
   * 6. Sign-in Details (account email & password)
   */
  function registerForm() {
    return {
      // ============================================
      // STATE MANAGEMENT
      // ============================================

      step: 1, // Current step (1-6)
      loading: false, // Submit button loading state
      error: '', // Error message display
      success: '', // Success message display
      file: null, // Profile picture file object

      // Taxonomy data fetched from WordPress API
      tax: {
        departments: [], // List of department terms
        roles: [] // List of role title terms
      },

      // ============================================
      // FORM DATA
      // ============================================

      form: {
        // Step 1: Basic Information
        first_name: '',
        last_name: '',
        phone_number: '',
        contact_email: '',
        keep_private: false,
        opt_out_marketing: false,

        // Step 2: Professional Info
        department: '', // Department taxonomy term ID
        role_title: '', // Role taxonomy term ID
        imdb_link: '',

        // Step 3: Location & Availability
        postcode: '',
        area_region: '',
        availability_status: 'available', // Options: 'available', 'booked', 'penciled'
        available_from: '', // Date (YYYY-MM-DD)
        available_until: '', // Date (YYYY-MM-DD)

        // Step 4: Work Experience (minimum 3 entries required)
        work_experience: [{
            we_role_title: '',
            we_production_name: '',
            we_date: ''
          },
          {
            we_role_title: '',
            we_production_name: '',
            we_date: ''
          },
          {
            we_role_title: '',
            we_production_name: '',
            we_date: ''
          },
        ],

        // Step 5: Profile Media (handled via file input, stored in this.file)

        // Step 6: Sign-in Details
        email: '', // WordPress login email
        password: '', // WordPress login password (min 8 chars)
        // captcha: ''       // Optional: Turnstile/reCAPTCHA token
      },

      // ============================================
      // LIFECYCLE & INITIALIZATION
      // ============================================

      /**
       * Alpine.js initialization hook
       * Loads taxonomy data when component mounts
       */
      init() {
        this.loadTaxonomies();
      },

      /**
       * Fetch department and role taxonomies from WordPress REST API
       * Populates dropdown options for Step 2 (Professional Info)
       */
      async loadTaxonomies() {
        try {
          const depRes = await fetch('/wp-json/wp/v2/department?per_page=100&orderby=name&order=asc');
          const roleRes = await fetch('/wp-json/wp/v2/role_title?per_page=100&orderby=name&order=asc');
          if (depRes.ok) this.tax.departments = await depRes.json();
          if (roleRes.ok) this.tax.roles = await roleRes.json();
        } catch (e) {
          console.warn('Taxonomy fetch failed', e);
        }
      },

      // ============================================
      // NAVIGATION & VALIDATION
      // ============================================

      /**
       * Advance to next step with validation
       * Step 3: Validates availability date logic
       * - "Available from" cannot be after "Available until"
       * - "Penciled" requires "Available from" date
       */
      next() {
        // Step 3 validation: Date logic check
        if (this.step === 3) {
          // Validate penciled status has required "from" date
          if (this.form.availability_status === 'penciled' && !this.form.available_from) {
            this.error = '"Available from" date is required when status is Penciled.';
            scrollTo(0, 0);
            return;
          }

          // Validate date order when both dates are present
          if (this.form.available_from && this.form.available_until) {
            if (new Date(this.form.available_from) > new Date(this.form.available_until)) {
              this.error = 'The "Available from" date cannot be after the "Available until" date.';
              scrollTo(0, 0);
              return;
            }
          }

          // Clear dates when booked (from should be empty)
          if (this.form.availability_status === 'booked') {
            this.form.available_from = '';
          }
        }

        this.error = ''; // Clear any errors
        if (this.step < 6) this.step++;
        scrollTo(0, 0);
      },

      /**
       * Real-time date validation for Step 3
       * Triggered on date field changes
       */
      validateDates() {
        // Validate date order when both dates are present
        if (this.form.available_from && this.form.available_until) {
          if (new Date(this.form.available_from) > new Date(this.form.available_until)) {
            this.error = 'The "Available from" date cannot be after the "Available until" date.';
          } else {
            this.error = '';
          }
        } else {
          this.error = '';
        }
      },

      /**
       * Go back to previous step
       * Clears error messages
       */
      prev() {
        this.error = ''; // Clear any errors
        if (this.step > 1) this.step--;
        scrollTo(0, 0);
      },

      // ============================================
      // WORK EXPERIENCE MANAGEMENT (Step 4)
      // ============================================

      /**
       * Add new work experience entry
       * User can add unlimited entries beyond the required 3
       */
      addExp() {
        this.form.work_experience.push({
          we_role_title: '',
          we_production_name: '',
          we_date: ''
        });
      },

      /**
       * Remove work experience entry by index
       * Minimum 3 entries enforced in UI (remove button hidden when <= 3)
       */
      removeExp(i) {
        this.form.work_experience.splice(i, 1);
      },

      // ============================================
      // FILE UPLOAD HANDLING (Step 5)
      // ============================================

      /**
       * Handle file selection from file input
       * Validates file type (JPG, JPEG, PNG only)
       * @param {Event} e - File input change event
       */
      onFile(e) {
        const selectedFile = e.target.files?.[0] || null;
        if (selectedFile) {
          // Validate file type against allowed formats
          const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
          const allowedExtensions = ['.jpg', '.jpeg', '.png'];
          const fileExtension = '.' + selectedFile.name.split('.').pop().toLowerCase();

          if (allowedTypes.includes(selectedFile.type) || allowedExtensions.includes(fileExtension)) {
            this.file = selectedFile;
            this.error = ''; // Clear any previous error
          } else {
            this.error = 'Please select a valid image file (JPG, JPEG, or PNG only)';
            this.file = null;
            e.target.value = ''; // Clear the input
          }
        } else {
          this.file = null;
        }
      },

      /**
       * Handle drag-and-drop file upload
       * Validates file type and syncs with file input
       * @param {DragEvent} e - Drop event
       */
      handleFileDrop(e) {
        e.target.classList.remove('drag-over');
        const files = e.dataTransfer.files;
        if (files.length > 0) {
          const droppedFile = files[0];

          // Validate file type against allowed formats
          const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
          const allowedExtensions = ['.jpg', '.jpeg', '.png'];
          const fileExtension = '.' + droppedFile.name.split('.').pop().toLowerCase();

          if (allowedTypes.includes(droppedFile.type) || allowedExtensions.includes(fileExtension)) {
            this.file = droppedFile;
            this.error = ''; // Clear any previous error
            // Sync dropped file with hidden file input for form consistency
            const fileInput = this.$refs.fileInput;
            const dt = new DataTransfer();
            dt.items.add(droppedFile);
            fileInput.files = dt.files;
          } else {
            this.error = 'Please select a valid image file (JPG, JPEG, or PNG only)';
            this.file = null;
          }
        }
      },

      // ============================================
      // FORM SUBMISSION (Step 6)
      // ============================================

      /**
       * Submit registration form to WordPress API
       * 
       * Process:
       * 1. Builds FormData with all form fields
       * 2. Converts work experience to JSON
       * 3. Attaches profile picture (if provided)
       * 4. Posts to /wp-json/site/v1/register
       * 5. Creates WordPress user + custom post type profile
       * 
       * API Response Expected:
       * {
       *   ok: true,
       *   profile_url: 'https://...',
       *   user_id: 123
       * }
       * 
       * @async
       */
      async submit() {
        this.loading = true;
        this.error = '';
        this.success = '';

        try {
          // Build FormData for multipart/form-data submission (supports file upload)
          const fd = new FormData();

          // Append all scalar fields and booleans
          const entries = {
            // Step 1: Basic Information
            first_name: this.form.first_name,
            last_name: this.form.last_name,
            phone_number: this.form.phone_number,
            contact_email: this.form.contact_email,
            keep_private: this.form.keep_private ? '1' : '0',
            opt_out_marketing: this.form.opt_out_marketing ? '1' : '0',

            // Step 2: Professional Info
            department: this.form.department, // Taxonomy term ID
            role_title: this.form.role_title, // Taxonomy term ID
            imdb_link: this.form.imdb_link,

            // Step 3: Location & Availability
            postcode: this.form.postcode,
            area_region: this.form.area_region,
            availability_status: this.form.availability_status,
            available_from: this.form.available_from || '',
            available_until: this.form.available_until || '',

            // Step 6: Sign-in Details
            email: this.form.email, // WordPress login email
            password: this.form.password,
            // captcha: this.form.captcha || ''         // Optional CAPTCHA token
          };
          Object.entries(entries).forEach(([k, v]) => fd.append(k, v ?? ''));

          // Step 4: Work Experience (serialize as JSON string)
          fd.append('work_experience', JSON.stringify(this.form.work_experience));

          // Step 5: Profile Picture (attach file blob)
          if (this.file) fd.append('picture', this.file);

          // POST to custom WordPress REST API endpoint
          const res = await fetch('/wp-json/site/v1/register', {
            method: 'POST',
            body: fd,
            credentials: 'same-origin'
          });

          const data = await res.json();

          // Handle API errors
          if (!res.ok || !data.ok) {
            throw new Error(data.error || 'Registration failed');
          }

          // Success! Show confirmation message
          this.success = 'Account created!';

          // Optional: Redirect to profile or dashboard
          if (data.profile_url) {
            // Uncomment to enable auto-redirect after registration
            // window.location.href = data.profile_url;
          }
        } catch (e) {
          // Display error message to user
          this.error = e.message || 'Something went wrong';
        } finally {
          // Reset loading state
          this.loading = false;
        }
      }
    }
  }
</script>