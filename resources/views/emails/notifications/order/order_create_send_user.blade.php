<style type="text/css">
  body,
  html, 
  .body {
    background: #f3f3f3 !important;
  }

</style>

<container>

  <spacer size="25"></spacer>
  <center>
    <img src="https://s3.amazonaws.com/tw-desk/i/67454/doclogo/107784.20170217165210371.107784.20170217165210371ZhR7Y.png" height="50" width="100" alt="WA Clean Platform" >
  </center>
  <spacer size="25"></spacer>
  <row>
    <columns>
      <h1 class="text-center">Thanks for your order.</h1>

      <spacer size="16"></spacer>

      <callout class="secondary">
        <row>
          <columns large="6">
            <p>
              <strong>Username</strong><br/>
              {{ $username }}
            </p>

            <p>
              <strong>Email Address</strong><br/>
              <a href="mailto:{{ $userEmail }}">{{ $userEmail }}</a>
            </p>
            <p>
              <strong>Supervisor Email</strong><br/>
              <a href="mailto:{{ $supervisorEmail }}">{{ $supervisorEmail }}</a> <br/>

            </p>

          </columns>
          <columns large="6">
            <p>
              <strong>Order ID</strong><br/>
              {{ $orderId }}
            </p>
            <p>
              <strong>Shipping Address</strong><br/>
              {{ $addressName }}<br/>
              {{ $addressAddress }}<br/>
              {{ $addressCity }},{{ $addressPostalCode }} {{ $addressCountry }}
            </p>
          </columns>
        </row>
      </callout>

      <h5>Service informations</h5>
      <p>{{ $serviceDescription }}</p>
      <callout class="primary">
      <table>
        <tr><td>domestic</td><td>voice</td><td>{{ $domesticvoice }}</td></tr>
        <tr><td>domestic</td><td>data</td><td>{{ $domesticdata }}</td></tr>
        <tr><td>domestic</td><td>messaging</td><td>{{ $domesticmessage }}</td></tr>
        <tr><td>international</td><td>voice</td><td>{{ $internationalvoice }}</td></tr>
        <tr><td>international</td><td>data</td><td>{{ $internationaldata }}</td></tr>
        <tr><td>international</td><td>messaging</td><td>{{ $internationalmessage }}</td></tr>
      </table>
      </callout>

      <hr/>
      <h5>Device Info</h5>
      <callout class="clean">

        <row>
          <columns large="6">
            <p>
              <strong>Mobile Number:</strong><br/>
              {{ $devicePhoneNo }}
            </p>
            <p>
              <strong>Carrier:</strong><br/>
              {{ $deviceCarrier }}
            </p>
          </columns>
          <columns large="6">
            <p>
              <strong>Make/Model:</strong><br/>
              {{ $deviceMake }} {{ $deviceModel }}
            </p>
            <p>
              <strong>Accessories:</strong><br/>
              {{ $deviceAccessories }}
            </p>
          </columns>
        </row>
      </callout>
      <h4>What's Next?</h4>

      <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Autem commodi dolorum ea eaque harum nihil non rem sequi vitae voluptas.</p>
      <hr/>
    </columns>
  </row>
  <row class="footer text-center">
    <columns large="6">
      <img src="https://cdn.frontify.com/api/screen/thumbnail/TNfyPhVEjFfz4ya2GF3cskO7pQU4Z51TmuoH8Z_u2aCYAK984FMJJ8nApcHrcsfT9ns5nBP3d9TNzVtgSv6-8g/1111" alt="">

    </columns>
    <columns large="6">
      <p>
        <strong>Call:</strong> 888.588.5550<br/>
        <strong>Email:</strong> support@discount.boat <br/>
        230 North St.
        Danvers, MA, 01923 <br/>
        USA<br/>
      </p>
    </columns>
  </row>
</container>
