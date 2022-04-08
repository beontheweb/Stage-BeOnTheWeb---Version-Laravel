@extends('template.index')

@section('main')
    <main class="m-4">
        <form action="{{ route('user.update', ['id' => $user->id]) }}" method="post" id="main" novalidate>
            @csrf
            @method('PATCH')

            <div class="form-group mb-3">
                <label class="col-sm-2 control-label" for="name">Nouveau Nom</label>
                <div class="col-sm-5">
                    <input id="name" class="form-control" type="text" placeholder="Nom" name="name"
                        value="{{ $user->name }}">
                </div>
                <div class="col-sm-5 messages">
                </div>
            </div>
            <div class="form-group mb-3">
                <label class="col-sm-2 control-label" for="email">Nouvel Email</label>
                <div class="col-sm-5">
                    <input id="email" class="form-control" type="email" placeholder="Email" name="email"
                        value="{{ $user->email }}">
                </div>
                <div class="col-sm-5 messages"></div>
            </div>
            <div class="form-group mb-3">
                <label class="col-sm-2 control-label" for="password">Mot de passe</label>
                <div class="col-sm-5">
                    <input id="password" class="form-control" type="password" placeholder="Mot de passe" name="password">
                </div>
                <div class="col-sm-5 messages"></div>
            </div>
            <div class="form-group mb-3">
                <label class="col-sm-2 control-label" for="password_confirmation">Confirmer le mot de passe</label>
                <div class="col-sm-5">
                    <input id="password_confirmation" class="form-control" type="password"
                        placeholder="Confirmer le mot de passe" name="password_confirmation">
                </div>
                <div class="col-sm-5 messages"></div>
            </div>
            <button type="submit" class="btn btn-primary me-3">Modifier</button>
            <a href="../users" class="btn btn-primary me-3">Annuler</a>

        </form>
    </main>
@endsection

@section('script')
    <script>
        (function() {
            // These are the constraints used to validate the form
            var constraints = {
                email: {
                    // Email is required
                    presence: false,
                    // and must be an email (duh)
                    email: true
                },
                name: {
                    // You need to pick a username too
                    presence: false,
                    format: {
                        // We don't allow anything that a-z and 0-9
                        pattern: "[a-z0-9]+",
                        // but we don't care if the username is uppercase or lowercase
                        flags: "i",
                        message: "^Le nom ne peut contenir que a-z et 0-9"
                    }
                },
                password: {
                    // Password is also required
                    presence: false,
                    // And must be at least 5 characters long
                    format: {
                        // We don't allow anything that a-z and 0-9
                        pattern: "(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}",
                        message: "^Le mot de passe doit contenir au min. 8 caractères, une minuscule, une majuscule et un caractère spécial."
                    }
                },
                "password_confirmation": {
                    // You need to confirm your password
                    presence: false,
                    // and it needs to be equal to the other password
                    equality: {
                        attribute: "password",
                        message: "^The passwords does not match"
                    }
                }
            };

            // Hook up the form so we can prevent it from being posted
            var form = document.querySelector("form#main");
            form.addEventListener("submit", function(ev) {
                ev.preventDefault();
                handleFormSubmit(form);
            });

            // Hook up the inputs to validate on the fly
            var inputs = document.querySelectorAll("input, textarea, select")
            for (var i = 0; i < inputs.length; ++i) {
                inputs.item(i).addEventListener("change", function(ev) {
                    var errors = validate(form, constraints) || {};
                    showErrorsForInput(this, errors[this.name])
                });
            }

            function handleFormSubmit(form, input) {
                // validate the form against the constraints
                var errors = validate(form, constraints) ? validate(form, constraints) : null;
                // then we update the form to reflect the results
                showErrors(form, errors || {});
                if (!errors) {
                    submitForm(form);
                }
            }

            // Updates the inputs with the validation errors
            function showErrors(form, errors) {
                // We loop through all the inputs and show the errors for that input
                _.each(form.querySelectorAll("input[name], select[name]"), function(input) {
                    // Since the errors can be null if no errors were found we need to handle
                    // that
                    if(input.name != "_token" && input.name != "_method"){
                        showErrorsForInput(input, errors && errors[input.name]);
                    }
                });
            }

            // Shows the errors for a specific input
            function showErrorsForInput(input, errors) {
                // This is the root of the input
                var formGroup = closestParent(input.parentNode, "form-group");
                    // Find where the error messages will be insert into
                var messages = formGroup.querySelector(".messages");
                // First we remove any old messages and resets the classes
                resetFormGroup(formGroup);
                // If we have errors
                if (errors) {
                    // we first mark the group has having errors
                    formGroup.classList.add("has-error");
                    // then we append all the errors
                    _.each(errors, function(error) {
                        addError(messages, error);
                    });
                } else {
                    // otherwise we simply mark it as success
                    formGroup.classList.add("has-success");
                }
            }

            // Recusively finds the closest parent that has the specified class
            function closestParent(child, className) {
                if (!child || child == document) {
                    return null;
                }
                if (child.classList.contains(className)) {
                    return child;
                } else {
                    return closestParent(child.parentNode, className);
                }
            }

            function resetFormGroup(formGroup) {
                // Remove the success and error classes
                formGroup.classList.remove("has-error");
                formGroup.classList.remove("has-success");
                // and remove any old messages
                _.each(formGroup.querySelectorAll(".help-block.error"), function(el) {
                    el.parentNode.removeChild(el);
                });
            }

            // Adds the specified error with the following markup
            // <p class="help-block error">[message]</p>
            function addError(messages, error) {
                var block = document.createElement("p");
                block.classList.add("help-block");
                block.classList.add("error");
                block.innerText = error;
                messages.appendChild(block);
            }

            function submitForm(form) {
                console.log("Test");
                form.submit();
            }
        })();
    </script>
@endsection
