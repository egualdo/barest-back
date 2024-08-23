<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
   
    <style>
      .fontSora{
        font-family: Arial,Helvetica,Verdana,sans-serif!important;
      }
      .fontLato{
        font-family: Arial,Helvetica,Verdana,sans-serif!important;
      }
    </style>
  </head>
  <body>
    <table align="center" id="Tabla_01" width="600"  border="0" cellpadding="0" cellspacing="0">
      <thead>
        <th style="vertical-align: baseline">
          <img style="border: none;" src="https://i.ibb.co/gVsvGQG/leftHead.png" width="auto" height="auto" alt="path-left">
        </th>
        <th style="width:80%;text-align:center">
          <img style="border: none;" src="https://i.ibb.co/PtZgvWR/Logo.png" width="auto" height="auto" alt="barest-logo">
        </th>

        <th style="vertical-align: top">
          <img style="border: none;" src="https://i.ibb.co/1JxGbXM/right-Head.png" width="auto" height="auto" alt="path-right">
        </th>
      </thead>

      <tbody>
        <tr style="text-align:center">
          <td align="center" colspan="3" style="width:100%">
            <div  style="max-width: 500px;width: 100%">
              <h1 class="fontSora"><b>Hola, {{$name}}:</b></h1>
            </div>
            <p>&nbsp;</p>
            <div style="width: 100%">
              <a class="fontLato">
                Has solicitado un código para poder acceder a tu cuenta, no lo compartas con nadie.
              </a>
            </div>
            <p>&nbsp;</p>
            <div  style="max-width: 330px;width: 100%;">
              <div style="width:100%;background-color:#EEEEEE;border-radius: 12px;padding:16px">
                <h1 class="fontSora"><b>{{$token}}</b></h1>
              </div>
            </div>
            <p>&nbsp;</p>
            <div style="width: 100%">
              <a class="fontLato">
                El código caduca en <b>30 minutos</b>, pasado este tiempo deberás pedir otro código.
              </a>
            </div>
          </td>
        </tr>
        <tr>
          <td align="center" colspan="3" style="width:100%">
            <table align="center" id="Tabla_02" width="400" border="0">
              <tr>
                <td colspan="3">
                  <div style="text-align: center;">
                    <p>&nbsp;</p>
                      <p class="fontLato">Si no has solicitado este código por favor
                        comunícate con nosotros a
                        <a href="mailto:contacto@barest.com">contacto@barest.com</a>
                        o llamando al +34 555 555 55
                      </p>
                    <p>&nbsp;</p>
                  </div>
                </td>
              </tr>
              <tr>
               
                  <td colspan="3" align="center">
                   <div style="text-align: center;clear: both;width: 100%;height: 50px;max-width: 160px;">
                    <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" style="float: left;margin-right: 5px;">
                      <img style="border: none;display:block" src="https://i.ibb.co/YfNT7Zh/facebook.png" width="50" height="auto" alt="facebook">
                    </a>

                     <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" style="float: left;margin-right: 5px;">
                      <img style="border: none;display:block" src="https://i.ibb.co/gWxjG67/instagram.png" width="50" height="auto" alt="instagram">
                    </a>

                    <a href="https://linkedin.com" target="_blank" rel="noopener noreferrer" style="float: left">
                      <img style="border: none;display:block" src="https://i.ibb.co/r3NbwXC/linkedin.png" width="50" height="auto" alt="linkedin">
                    </a>
                    </div>
                  </td>
                
              </tr>
            </table>
          </td>
        </tr>
      </tbody>
    </table>
  </body>
</html>
