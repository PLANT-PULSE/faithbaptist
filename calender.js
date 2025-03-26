document.addEventListener("DOMContentLoaded", () => {
  // Tab switching functionality
  const tabBtns = document.querySelectorAll(".tab-btn")
  const tabContents = document.querySelectorAll(".tab-content")

  tabBtns.forEach((btn) => {
    btn.addEventListener("click", function () {
      // Remove active class from all buttons and contents
      tabBtns.forEach((b) => b.classList.remove("active"))
      tabContents.forEach((c) => c.classList.remove("active"))

      // Add active class to clicked button and corresponding content
      this.classList.add("active")
      const tabId = this.getAttribute("data-tab")
      document.getElementById(tabId).classList.add("active")
    })
  })

  // Amount selection functionality
  const amountBtns = document.querySelectorAll(".amount-btn")
  const customAmountDiv = document.querySelector(".custom-amount")
  const customAmountInput = document.getElementById("customAmount")
  const summaryAmount = document.getElementById("summaryAmount")
  const summaryTotal = document.getElementById("summaryTotal")

  // For recurring donation form
  const recurringAmountBtns = document.querySelectorAll("#recurring .amount-btn")
  const recurringCustomAmountDiv = document.querySelector("#recurring .custom-amount")
  const recurringCustomAmountInput = document.getElementById("recurringCustomAmount")
  const recurringSummaryAmount = document.getElementById("recurringSummaryAmount")
  const recurringSummaryTotal = document.getElementById("recurringSummaryTotal")

  // Function to handle amount selection
  function handleAmountSelection(buttons, customDiv, customInput, summaryAmountEl, summaryTotalEl) {
    buttons.forEach((btn) => {
      btn.addEventListener("click", function () {
        // Remove active class from all buttons
        buttons.forEach((b) => b.classList.remove("active"))

        // Add active class to clicked button
        this.classList.add("active")

        const amount = this.getAttribute("data-amount")

        if (amount === "custom") {
          // Show custom amount input
          customDiv.style.display = "block"
          customInput.focus()

          // Update summary with custom amount if it has a value
          if (customInput.value) {
            const customAmount = Number.parseFloat(customInput.value).toFixed(2)
            summaryAmountEl.textContent = `$${customAmount}`
            summaryTotalEl.textContent = `$${customAmount}`
          }
        } else {
          // Hide custom amount input
          customDiv.style.display = "none"

          // Update summary with selected amount
          summaryAmountEl.textContent = `$${amount}.00`
          summaryTotalEl.textContent = `$${amount}.00`
        }
      })
    })

    // Handle custom amount input changes
    if (customInput) {
      customInput.addEventListener("input", function () {
        const customAmount = Number.parseFloat(this.value).toFixed(2)
        if (!isNaN(customAmount)) {
          summaryAmountEl.textContent = `$${customAmount}`
          summaryTotalEl.textContent = `$${customAmount}`
        }
      })
    }
  }

  // Initialize amount selection for both forms
  handleAmountSelection(amountBtns, customAmountDiv, customAmountInput, summaryAmount, summaryTotal)

  handleAmountSelection(
    recurringAmountBtns,
    recurringCustomAmountDiv,
    recurringCustomAmountInput,
    recurringSummaryAmount,
    recurringSummaryTotal,
  )

  // Payment method selection
  const paymentBtns = document.querySelectorAll(".payment-btn")
  const cardDetails = document.querySelector(".card-details")

  paymentBtns.forEach((btn) => {
    btn.addEventListener("click", function () {
      // Remove active class from all buttons
      paymentBtns.forEach((b) => b.classList.remove("active"))

      // Add active class to clicked button
      this.classList.add("active")

      const paymentMethod = this.getAttribute("data-payment")

      // Show/hide card details based on payment method
      if (paymentMethod === "card") {
        cardDetails.style.display = "block"
      } else {
        cardDetails.style.display = "none"
      }
    })
  })

  // Transaction fee checkbox functionality
  const coverFeesCheckbox = document.getElementById("coverFees")
  const feesSummary = document.getElementById("feesSummary")
  const summaryFees = document.getElementById("summaryFees")

  coverFeesCheckbox.addEventListener("change", function () {
    const currentAmount = Number.parseFloat(summaryAmount.textContent.replace("$", ""))

    if (this.checked) {
      // Show fees in summary
      feesSummary.style.display = "flex"

      // Calculate 3% fee
      const fee = (currentAmount * 0.03).toFixed(2)
      summaryFees.textContent = `$${fee}`

      // Update total
      const newTotal = (currentAmount + Number.parseFloat(fee)).toFixed(2)
      summaryTotal.textContent = `$${newTotal}`
    } else {
      // Hide fees in summary
      feesSummary.style.display = "none"

      // Reset total to original amount
      summaryTotal.textContent = `$${currentAmount.toFixed(2)}`
    }
  })

  // Recurring donation frequency selection
  const frequencySelect = document.getElementById("frequency")
  const summaryFrequency = document.getElementById("summaryFrequency")

  frequencySelect.addEventListener("change", function () {
    summaryFrequency.textContent = this.options[this.selectedIndex].text
  })

  // Form submission handling
  const donationForm = document.getElementById("donationForm")
  const recurringDonationForm = document.getElementById("recurringDonationForm")

  function handleFormSubmission(form) {
    form.addEventListener("submit", (e) => {
      e.preventDefault()

      // Get form data
      const formData = new FormData(form)

      // In a real application, you would send this data to your payment processor
      // For this demo, we'll just show a success message

      // Change button text and add loading spinner
      const submitBtn = form.querySelector('button[type="submit"]')
      const originalText = submitBtn.textContent
      submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...'
      submitBtn.disabled = true

      // Simulate form submission
      setTimeout(() => {
        // Replace form with success message
        const formCard = form.closest(".form-card")
        formCard.innerHTML = `
                    <div class="donation-success">
                        <i class="fas fa-check-circle"></i>
                        <h3>Thank You for Your Donation!</h3>
                        <p>Your generous contribution helps us continue our mission and serve our community. A confirmation email has been sent to your registered email address with the details of your donation.</p>
                        <div class="donation-details">
                            <div class="detail-item">
                                <span>Donation Amount:</span>
                                <span>${form.id === "donationForm" ? summaryTotal.textContent : recurringSummaryTotal.textContent}</span>
                            </div>
                            ${
                              form.id === "recurringDonationForm"
                                ? `
                            <div class="detail-item">
                                <span>Frequency:</span>
                                <span>${summaryFrequency.textContent}</span>
                            </div>
                            `
                                : ""
                            }
                            <div class="detail-item">
                                <span>Transaction ID:</span>
                                <span>${Math.random().toString(36).substring(2, 15)}</span>
                            </div>
                            <div class="detail-item">
                                <span>Date:</span>
                                <span>${new Date().toLocaleDateString()}</span>
                            </div>
                        </div>
                        <p class="mt-20">If you have any questions about your donation, please contact our church office.</p>
                        <a href="index.html" class="btn btn-primary mt-20">Return to Homepage</a>
                    </div>
                `
      }, 2000)
    })
  }

  // Initialize form submission handling
  handleFormSubmission(donationForm)
  handleFormSubmission(recurringDonationForm)

  // FAQ accordion functionality
  const faqItems = document.querySelectorAll(".faq-item")

  faqItems.forEach((item) => {
    const question = item.querySelector(".faq-question")
    const answer = item.querySelector(".faq-answer")
    const icon = question.querySelector("i")

    question.addEventListener("click", () => {
      // Toggle active class
      item.classList.toggle("active")

      // Toggle icon
      if (item.classList.contains("active")) {
        icon.classList.remove("fa-plus")
        icon.classList.add("fa-minus")
        answer.style.maxHeight = answer.scrollHeight + "px"
      } else {
        icon.classList.remove("fa-minus")
        icon.classList.add("fa-plus")
        answer.style.maxHeight = "0"
      }
    })
  })
})

