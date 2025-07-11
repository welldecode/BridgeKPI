const mercadopago = new MercadoPago('TEST-c8fdf9ec-ce5e-410f-ab7d-716fe8595c97');

const cardForm = mercadopago.cardForm({
    amount: ($("#amount").val()),
    iframe: true,
    form: {
        id: "form-checkout",
        cardNumber: {
            id: "form-checkout__cardNumber",
            placeholder: "Número do cartão",
        },
        expirationDate: {
            id: "form-checkout__expirationDate",
            placeholder: "MM/YY",
        },
        securityCode: {
            id: "form-checkout__securityCode",
            placeholder: "Código de segurança",
        },
        cardholderName: {
            id: "form-checkout__cardholderName",
            placeholder: "Titular do cartão",
        },
        issuer: {
            id: "form-checkout__issuer",
            placeholder: "Banco emissor",
        },
        installments: {
            id: "form-checkout__installments",
            placeholder: "Parcelas",
        },
        identificationType: {
            id: "form-checkout__identificationType",
            placeholder: "Tipo de documento",
        },
        identificationNumber: {
            id: "form-checkout__identificationNumber",
            placeholder: "Número do documento",
        },
        cardholderEmail: {
            id: "form-checkout__cardholderEmail",
            placeholder: "E-mail",
        },
    },
    callbacks: {
        onFormMounted: (error) => {
            if (error) return console.warn("Form Mounted handling error: ", error);
            console.log("Form mounted");
        },
        onSubmit: event => {
            event.preventDefault();

            $.ajax({
                url: '/payments/process_payment',
                method: 'POST',
                enctype: 'multipart/form-data',
                data: JSON.stringify(cardForm.getCardFormData()),
                processData: false,
                contentType: false,
                dataType: 'json',
                headers: {
                    "Content-Type": "application/json", 'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                },
                success: function (response) {
                    const redirect = response.redirect;
                    const type = response.payment_method;

                    window.location.href = "/pay/order/" + redirect + "/" + type;

                    console.log(response);
                },
                error: function (response) {
                    console.log(response);
                }
            })
        },
        onFetching: (resource) => {
            console.log(resource);
        }
    },
});


$('#form-checkout_submit').click(function (e) {

    e.preventDefault();
    var filter_payment = $("input[name='filter_payment']:checked").val();

    console.log(filter_payment);

    const type = {
        paymentMethodId: (cardForm.getCardFormData()['paymentMethodId'] ? cardForm.getCardFormData()['paymentMethodId'] : filter_payment),
        amount: parseFloat($("#amount").val()),
        card: (cardForm.getCardFormData()['paymentMethodId'] ? cardForm.getCardFormData() : null),
    };

    $.ajax({
        url: '/payments/process_payment',
        method: 'POST',
        enctype: 'multipart/form-data',
        data: JSON.stringify(type),
        processData: false,
        contentType: false,
        dataType: 'json',
        headers: {
            "Content-Type": "application/json", 'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
        },
        success: function (response) {
            const redirect = response.redirect;
            const type = response.payment_method;
            window.location.href = "/pay/order/" + redirect + "/" + type;
            console.log(response);
        },
        error: function (response) {
            console.log(response);
        }
    })

}) 