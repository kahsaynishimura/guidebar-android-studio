var geocoder;
var map;
var marker = new google.maps.Marker();
var markerDisabled = new google.maps.Marker();

/**********   cadastro de endereço     ***********/
function initializeMap() {

    geocoder = new google.maps.Geocoder();
    var latlng = new google.maps.LatLng(-34.397, 150.644);

    var mapOptions = {
        zoom: 9,
        center: latlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
    if ($("#latitude").val() === "") {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(pos) {
                map.setCenter(new google.maps.LatLng(pos.coords.latitude, pos.coords.longitude));
            }, function(error) {
                alert(error);
            });
        }
    } else {
        var lat = parseFloat($("#latitude").val());
        var lng = parseFloat($("#longitude").val());
        var mapOptions = {
            zoom: 9,
            center: new google.maps.LatLng(lat, lng),
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
    }

}

function placeMarker(location) {
    markerDisabled.setMap(null);
    marker.setPosition(location);

    $("#latitude").val(location.k);
    $("#longitude").val(location.D);
}
function placeMarkerDisabled() {
    var lat = parseFloat($("#latitude").val());
    var lng = parseFloat($("#longitude").val());
    var location = new google.maps.LatLng(lat, lng);

    var mapOptions = {
        zoom: 9,
        center: location,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
    marker.setMap(null);
    markerDisabled.setMap(map);
    markerDisabled.setPosition(location);
}
function codeAddress() {
    var address = $("#street").val() + "," +
            $("#street_number").val() + "," +
            $("#AddressCityId option:selected").text() + "-" +
            $("#AddressStateId option:selected").text() + "," +
            $("#zip_code").val();
    geocoder.geocode({'address': address}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            map.setCenter(results[0].geometry.location);
            placeMarker(results[0].geometry.location);

            $("#divMapInstructions").show();
        } else {
            alert("Não foi possível localizar este endereço. Por favor, marque no mapa a posição onde acontecerá o evento.<br>");
        }
    });
}

function resetAddressFields() {
    marker.setMap(map);
    markerDisabled.setMap(null);
    google.maps.event.addListener(map, 'click', function(event) {
        placeMarker(event.latLng);
    });

    $("#place_name").prop("disabled", false);
    $("#street").prop("disabled", false);
    $("#street_number").prop("disabled", false);
    $("#complement").prop("disabled", false);
    $("#neighborhood").prop("disabled", false);
    $("#zip_code").prop("disabled", false);
    $("#AddressStateId").prop("disabled", false);
    $("#AddressCityId").prop("disabled", false);

    $("#divAddressForm").show();
    $("#divMapInstructions").hide();
    google.maps.event.trigger(map, 'resize');
    $("#divAddressPresentation").hide();
}
function updateAddressfields() {
    if ($("#userAddresses option:selected").val() === "0")
    {
        resetAddressFields();
    } else {
        $.ajax({
            type: 'POST',
            url: "/addresses/view/" + $("#userAddresses option:selected").val(),
            dataType: 'json',
            beforeSend: function() {
                $('#divEventAddressLoading').html('<div><center><span><img alt=""  src="/img/loading_gif_32x32.gif"></span></center></div>');
                $('#divEventAddressLoading').show();
                $("#divMap").hide();
                $("#divAddressForm").hide();
                $("#divEventAddress").hide();
            },
            success: function(response, textStatus, xhr) {
                $("#latitude").val(response.Address.latitude);
                $("#longitude").val(response.Address.longitude);

                $("#divAddressPresentation").html(
                        "<span style='font-weight:bold;'>" + response.Address.place_name + "</span><br>" +
                        response.Address.street + ", " + response.Address.street_number + " " + response.Address.complement + "<br>" +
                        response.Address.neighborhood + " - " + response.City.name + "/" + response.City.State.uf + "<br>" +
                        response.Address.zip_code
                        );

                $("#place_name").prop("disabled", true);
                $("#street").prop("disabled", true);
                $("#street_number").prop("disabled", true);
                $("#complement").prop("disabled", true);
                $("#neighborhood").prop("disabled", true);
                $("#zip_code").prop("disabled", true);
                $("#AddressStateId").prop("disabled", true);
                $("#AddressCityId").prop("disabled", true);
            },
            complete: function() {
                $('#divEventAddressLoading').hide();
                $("#divAddressPresentation").show();
                $("#divEventAddress").show();

                $("#divMap").show();
                google.maps.event.clearListeners(map, 'click');
                placeMarkerDisabled();
                $("#userAddresses").focus();
            },
            error: function(xhr, textStatus, error) {
                $("#AddressData_div").html('<div class="flash-note-red" style=""><span>Opps...</span></div>');
            }
        });
    }
}

function setIsActive(isActive) {
    $("#is_active").val(isActive);
    $("#EventAddForm").submit();
}