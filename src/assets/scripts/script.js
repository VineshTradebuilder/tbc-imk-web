"use strict";
/********************************
 * @Function Utilities
 ********************************/

function _alert(alertContainer) {
    this.elem = alertContainer ? $(alertContainer) : $(".contact-message");

    this.reset = function () {
        this.elem.removeClass(" alert-danger alert-success alert-info hide");
    };

    this.error = function (message) {
        this.reset();
        this.elem.addClass("alert-danger").html(message);
        this.hide();
    };

    this.success = function (message) {
        this.reset();
        this.elem.addClass("alert-success").html(message);
        this.hide();
    };

    this.info = function (message) {
        this.reset();
        this.elem.addClass("alert-info").html(message);
        this.hide();
    };

    var that = this;
    this.hide = function () {
        setTimeout(function () {
            that.elem.addClass(" hide");
            that.elem.html("");
        }, 4000);
    };
}

function _c(elem, attributes, children) {
    var elemObj = document.createElement(elem);
    for (var ak in attributes) {
        elemObj.setAttribute(ak, attributes[ak]);
    }

    for (var ck in children) {
        if (typeof children[ck] == 'object') {
            elemObj.appendChild(children[ck]);
        } else {
            elemObj.innerHTML = children;
        }
    }
    return elemObj;
}

function objectifyForm(formArray) {
    //serialize data function
    returnArray = {};
    for (var i = 0; i < formArray.length; i++) {
        returnArray[formArray[i]["name"]] = formArray[i]["value"];
    }
    return returnArray;
}

function humanize(str) {
    var frags = str.split('_');
    for (i = 0; i < frags.length; i++) {
        frags[i] = frags[i].charAt(0).toUpperCase() + frags[i].slice(1);
    }
    return frags.join(' ');
}

function formParams(formData) {
    return JSON.parse('{"' + decodeURI(unescape(formData)).replace(/"/g, '\\"').replace(/&/g, '","').replace(/=/g, '":"') + '"}')
}

/********************************
 * End Utilities
 ********************************/

/********************************
 * Local API Call
 ********************************/

var walkScoreHtml = "";
var nearByHtml = {};
var schoolHtml = "";
function getWalkscore(property_location, elem, dontFetch) {
    if (dontFetch && walkScoreHtml != "") {
        return;
    }
    var url = siteUrl + '/api-provider/getWalkScore';
    $.post(url, property_location).done(function (data) {
        data = JSON.parse(data);
        walkScoreHtml = '<img src="' + data.logo_url + '" class="imageLoader" >  &nbsp; <span >' + data.walkscore + '</span> out of 100';
        $(elem).html(walkScoreHtml);
    }).fail(function () {
        $(elem).html("<h3>We could not find the information for this specific property, Please try again after some time.</h3>");
    })
}

function getWalkscoreJson(property_location) {
    var url = siteUrl + '/api-provider/getWalkScore';
    return $.post(url, property_location);
}

function getNearBy(term, property_location, elem, dontFetch) {

    if (dontFetch && nearByHtml[term] != undefined) {
        return;
    }
    nearByHtml[term] = "";
    var url = siteUrl + '/api-provider/getNearBy';
    property_location['term'] = term;
    $.post(url, property_location).done(function (data) {
        data = JSON.parse(data);
        if (data.businesses) {
            data = data.businesses;
        }
        for (var i = 0; i < data.length; i++) {
            var neadBytemp = data[i];
            nearByHtml[term] += '<div class="nearbyItem">' +
                    '            <div class="nearby-thumb" style="background-image:url(\' ' + neadBytemp.image_url + ' \')"></div>' +
                    '            <div class="nearby-info">' +
                    '                <div class="nearby-title">' +
                    '                    ' + neadBytemp.name + '    ' +
                    '                </div>' +
                    '                <div class="nearby-description">' +
                    '                    <ul>' +
                    '                        <li>Address: ' + neadBytemp.location.display_address[0] + ', ' + neadBytemp.location.display_address[1] + ' <br><b>Distance: </b>' + Math.round(neadBytemp.distance) + ' (m) </li>' +
                    '                        ' +
                    '                        <li> Phone: ' + neadBytemp.display_phone + ' </li>' +
                    '                        <li> Rating: ' + neadBytemp.rating + ' (Based on ' + neadBytemp.review_count + ' review(s) ) </li>' +
                    '                        <li> <a target="__blank" href="' + neadBytemp.url + '" > Read More </a></li>' +
                    '                    </ul>' +
                    '                </div>' +
                    '            </div>' +
                    '        </div>';
        }

        $(elem).html(nearByHtml[term]);

    }).fail(function () {
        $(elem).html("<h3>We could not find the information for this specific property, Please try again after some time.</h3>");
    })
}

function getNearByJson(term, property_location) {
    var url = siteUrl + '/api-provider/getNearBy';
    property_location['term'] = term;
    return $.post(url, property_location)
}

function getSchools(property_location, elem, dontFetch) {
    var url = siteUrl + '/api-provider/getSchools';
    if (dontFetch && schoolHtml != "") {
        return;
    }
    $.post(url, property_location).done(function (data) {
        data = JSON.parse(data);
        for (var i = 0; i < data.length; i++) {
            var school = data[i];
            schoolHtml += "<tr>" +
                    "<td><b > <a target='__blank' href='"+ school.overviewLink +"'> " + school.name + " </a> </b> <br> "+school.type+" </td>" +
                    "<td><span class='distance'> <i class='fa fa-street-view' aria-hidden='true'></i> " + school.distance + " </span></td>" +
                    "<td>" + school.parentRating + "</td>" +
                    "<td>" + school.gradeRange + "</td>" +
                    "</tr>";
        }

        $(elem).html(schoolHtml);
    }).fail(function () {
        $(elem).html("<h3>We could not find the information for this specific property, Please try again after some time.</h3>");
    })
}

function getSchoolsJson(property_location) {
    var url = siteUrl + '/api-provider/getSchools';
    return $.post(url, property_location);
}

/********************************
 * End Local API Call
 ********************************/