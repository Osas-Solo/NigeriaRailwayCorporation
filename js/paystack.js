const paymentForm = document.getElementById("checkout-form");
const makePaymentButton = document.getElementById("make-payment");

let transactionReference = document.getElementById("transaction-reference");
const emailAddress = document.getElementById("email-address").value;
const ticketPrice = document.getElementById("ticket-price").value;

paymentForm.addEventListener("submit", payWithPaystack, false);

function payWithPaystack(e) {
    e.preventDefault();

    let handler = PaystackPop.setup({
        key: "pk_test_7f3ab30aaaac40e0f1b70ea4393c98ca03a00e82",
        email: emailAddress,
        amount: ticketPrice * 100,

        onClose: function() {
            alert("Cancel transaction?");
        },

        callback: function(response) {            
            paymentForm.removeEventListener("submit", payWithPaystack, false);
            transactionReference.value = response.reference;
            confirmReservation();
        }   //  end of callback
    });

    handler.openIframe();
}   //  end of payWithPaystack()

function confirmReservation() {
    paymentForm.setAttribute("action", "check-out.php");
    paymentForm.setAttribute("method", "POST");
    makePaymentButton.click();
}   //  end of confirmReservation()