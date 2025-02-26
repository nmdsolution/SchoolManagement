<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>Invoice</title>

    <style>

        .columns-two {
            -moz-column-count: 2;
            -moz-column-gap: 6px;
            -webkit-column-count: 2;
            -webkit-column-gap: 6px;
            column-count: 2;
            column-gap: 6px;
        }


        .invoice-box {
            max-width: 800px;
            margin: auto;
            /* padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15); */
            font-size: 15px;
            line-height: 24px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            /* text-align: left; */
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }


        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 20px;
            line-height: 20px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
            text-align: center;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
            text-align: center;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }

        @media only screen and (max-width: 600px) {
            .invoice-box table tr.top table td {
                width: 100%;
                display: block;
                text-align: center;
            }

            .invoice-box table tr.information table td {
                width: 100%;
                display: block;
                text-align: center;
            }
        }

        /** RTL **/
        .invoice-box.rtl {
            direction: rtl;
            font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        }

        .invoice-box.rtl table {
            text-align: right;
        }

        .invoice-box.rtl table tr td:nth-child(2) {
            text-align: left;
        }


        .Booking-Table table tr.heading th {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
            text-align: center;

        }

        .Booking-Table table td, table th {

            padding: 5px 4px;
        }

        .Booking-Table table tr.item td {
            border-bottom: 1px solid #eee;
            text-align: center;
        }

        .page-break {
            page-break-after: always;
        }

        footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            font-size: 12px;
        }

        header {
            top: 0;
        }


    </style>

</head>

<body>


<div class="invoice-box">

    <table cellpadding="0" cellspacing="0">
        <tr>
            <td style="font-size: 14px" colspan="3">Aras & Aras GmbH - Freischützstr. 9 - 81927 München
                <hr>
            </td>
            <td colspan="4">

            </td>
        </tr>

        <tr class="top">
            <td colspan="7">
                <table>
                    <tr>
                        <td>
                            Company name <br>
                            Timesequare 262 - 263<br>
                            370001
                        </td>
                        <td class="title">
                            <img src="https://www.gravatar.com/avatar/205e460b479e2e5b48aec07710c08d50" style="width: 25%; max-width: 300px"/>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>


        <tr class="information">
            <td colspan="7">
                <table>
                    <tr>
                        <td>

                        </td>

                        <td>
                            'Rechnungs-Nr<br>
                            Bearbeiter: <br>
                            Stornorechnung Datum <br>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td colspan="7">
                <h1>Rechnung</h1>
            </td>

        </tr>


        <tr>
            <td colspan="7" style="font-size: 13px">Sehr geehrte Damen und Herren,</td>
        </tr>

        <tr>
            <td colspan="7" style="font-size: 13px">wir bedanken uns für die Beauftragung und stellen Ihnen gemäß dem Auftrag folgende Dienstleistung in Rechnung:</td>
        </tr>
        <tr>
            <td colspan="7">
                <table style="width: 100%">
                    <tr class="heading">
                        <td>Pos</td>
                        <td>Crowd</td>
                        <td>Description</td>
                        <td style="width: 10%;">Price&nbsp;<span><small>Net</small></span></td>
                        <td style="width: 10%;">St</td>
                        <td>Discount')}}</td>
                        <td>Amount&nbsp;<span><small>Net</small></span></td>
                    </tr>


                    <tr class="item">
                        <td style="width: 5px">1</td>
                        <td style="width: 5px">qty</td>
                        <td style="width: 50%;">description</td>
                        <td>price €</td>
                        <td>tax %</td>
                        <td>discount %</td>
                        <td>amount €</td>
                    </tr>
                    <tr class="item">
                        <td style="width: 5px">1</td>
                        <td style="width: 5px">qty</td>
                        <td style="width: 50%;">description</td>
                        <td>price €</td>
                        <td>tax %</td>
                        <td>discount %</td>
                        <td>amount €</td>
                    </tr>
                    <tr class="item">
                        <td style="width: 5px">1</td>
                        <td style="width: 5px">qty</td>
                        <td style="width: 50%;">description</td>
                        <td>price €</td>
                        <td>tax %</td>
                        <td>discount %</td>
                        <td>amount €</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr class="item">
            <td colspan="5"></td>
            <td><label
                        class="form-label col-md-7"><strong>Sub Total</strong></label>
            </td>
            <td class="text-center"><label
                        class="form-label"><strong><span
                                id="sub_total">100</span>&nbsp;€</strong></label>
            </td>
        </tr>


        <tr class="item">
            <td colspan="5"></td>
            <td>
                <label class="form-label col-md-7 mt-2"><strong>Discount</strong>(%)</label>

            </td>
            <td class="text-center"><label
                        class="form-label mt-1"><strong><span
                                id="discount_amount">100</span>&nbsp;€</strong></label>
            </td>
        </tr>


        <tr class="item">
            <td colspan="5"></td>
            <td><label
                        class="form-label col-md-7"><strong>Total</strong>&nbsp;<small>Net</small></label>
            </td>
            <td class="text-center"><label
                        class="form-label"><strong><span
                                id="total">100</span>&nbsp;€</strong></label>
            </td>
        </tr>


        <tr class="item">
            <td colspan="5"></td>
            <td><label class='form-label col-md-7'><strong>Tax (19%)</strong></label></td>
            <td class='text-center'><label class='form-label'><strong><span>100</span>&nbsp;€</strong></label><input type='hidden' name='hidden_per_nineteen' value='100'>
            </td>
        </tr>


        <tr class="item">
            <td colspan="5"></td>
            <td>
                <label class='form-label col-md-7'><strong>Tax (7%)</strong></label>
            </td>
            <td class='text-center'>
                <label class='form-label'><strong><span>100</span>&nbsp;€</strong></label><input type='hidden' name='hidden_per_seven' value=''>
            </td>
        </tr>


        <tr class="item" style=" margin-bottom: 100px;">
            <td colspan="5"></td>
            <td><label class="form-label"><strong>Grand Total</strong></label></td>
            <td class="text-center">
                <label class="form-label "><strong><span id="grand_total">100</span>&nbsp;€</strong></label>
            </td>
        </tr>
        <tr>
            <td colspan="7"></td>
        </tr>
        <tr>
            <td colspan="7"></td>
        </tr>
        <tr>
            <td colspan="7"></td>
        </tr>
        <tr>
            <td colspan="7" style="font-size: 13px">

                Bitte begleichen Sie den Gesamtbetrag von 100€ bis zum
                auf eines unserer Bankkonten.<br>


                Bei Rückfragen stehe ich Ihnen wie gewohnt jederzeit gerne zur Verfügung.<br>
                Mit freundlichen Grüße<br><br>
                Sagar Gor
            </td>

        </tr>


        <tr>
            <td colspan="7"></td>
        </tr>
        <tr>
            <td colspan="7"></td>
        </tr>
        <tr>
            <td colspan="7"></td>
        </tr>


    </table>
</div>

<footer>
    <hr>
    <table style="width: 100%">
        <tr>

            <td style="padding-right: 50px">
                FM Limo Server GmbH <br>
                GF: Farhad Test <br>
                Ohlauerstraße 2 <br>
                80997 München
            </td>

            <td style="padding-right: 50px">
                fm@urbnways-sandbox.com<br>
                www.ubrnways-sandbox.com<br>
                08913957089<br>
                0891205670
            </td>

            <td style="padding-right: 50px">
                LH München<br>
                HRB 273133<br>
                Str.-Nr: 181/815/08155<br>
                USt-ID: DE208344344

            </td>

            <td style="padding-right: 5px">
                Sparkasse München<br>
                DE02120300000000202051<br>
                SSKMDEMMXXX
            </td>
        </tr>
    </table>
</footer>


<div class="page-break item"></div>
<div class="Booking-Table Section1">
    <h1>Rides</h1>
    <br>
    <table style="width: 100%;font-size: 15px;">
        <thead>
        <tr class="heading">
            <th scope="col">#</th>
            <th scope="col">date</th>
            <th scope="col">customer</th>
            <th scope="col">begin</th>
            <th scope="col">goal</th>
            <th scope="col">Amount</th>
        </tr>
        </thead>
        <tbody>
        <tr class="item">
            <td>1</td>
            <td>date_time</td>
            <td>passenger</td>
            <td>pickup_address</td>
            <td>drop_address</td>
            <td>amount €</td>
        </tr>
        <tr class="item">
            <td>1</td>
            <td>date_time</td>
            <td>passenger</td>
            <td>pickup_address</td>
            <td>drop_address</td>
            <td>amount €</td>
        </tr>
        <tr class="item">
            <td>1</td>
            <td>date_time</td>
            <td>passenger</td>
            <td>pickup_address</td>
            <td>drop_address</td>
            <td>amount €</td>
        </tr>
        <tr class="item">
            <td>1</td>
            <td>date_time</td>
            <td>passenger</td>
            <td>pickup_address</td>
            <td>drop_address</td>
            <td>amount €</td>
        </tr>
        </tbody>
    </table>
</div>
</body>
</html>