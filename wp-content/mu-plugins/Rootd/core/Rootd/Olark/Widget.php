<?php

/**
 * Olark chat widget.
 *
 * PHP Version 5
 *
 * @todo      Develop Bronze, Gold, Platinum and Ultimate account configurations.
 * 
 * @package   Rootd
 * @author    Rick Buczynski <me@rickbuczynski.com>
 * @copyright 2014 Rick Buczynski. All Rights Reserved.
 */

class Rootd_Olark_Widget extends Rootd_Widget
{

    protected $_instanceId = '';

    /**
     * Internal constructor.
     * 
     * @return void
     */
    public function _construct()
    {
        $this->_init(
            'olark_chat_widget', 
            $this->__('Olark Chat Widget'),
            array(
                'description' => $this->__('Add Olark live chat to your site.'),
            )
        );

        $this
            ->setTemplate('form', 'Olark/Widget/Form.phtml')
            ->setTemplate('widget', 'Olark/Widget/Widget.phtml');

        $this->_instanceId = uniqid('olark_');
        $this->_renderArea = 'core';
    }

    /**
     * Generate the olark JS code.
     * 
     * @param string $serviceId     The 3rd-party Olark service ID.
     * @param string $configuration Custom Olark API JS configuration code.
     * 
     * @return string
     */
    public function getInstallationScript($serviceId = null, $configuration = null)
    {
        $data = $this->getWidgetData();

        // Pull from widget config if not provided
        if (is_null($serviceId)) {
            $serviceId = $data->getOlarkSiteId();
        }

        if (is_null($configuration)) {
            $configuration = $data->getOlarkCustomConfiguration();
        }

        ob_start();

print <<<EOL
        <!-- begin olark code -->
        <script data-cfasync="false" type='text/javascript'>/*<![CDATA[*/window.olark||(function(c){var f=window,d=document,l=f.location.protocol=="https:"?"https:":"http:",z=c.name,r="load";var nt=function(){
        f[z]=function(){
        (a.s=a.s||[]).push(arguments)};var a=f[z]._={
        },q=c.methods.length;while(q--){(function(n){f[z][n]=function(){
        f[z]("call",n,arguments)}})(c.methods[q])}a.l=c.loader;a.i=nt;a.p={
        0:+new Date};a.P=function(u){
        a.p[u]=new Date-a.p[0]};function s(){
        a.P(r);f[z](r)}f.addEventListener?f.addEventListener(r,s,false):f.attachEvent("on"+r,s);var ld=function(){function p(hd){
        hd="head";return["<",hd,"></",hd,"><",i,' onl' + 'oad="var d=',g,";d.getElementsByTagName('head')[0].",j,"(d.",h,"('script')).",k,"='",l,"//",a.l,"'",'"',"></",i,">"].join("")}var i="body",m=d[i];if(!m){
        return setTimeout(ld,100)}a.P(1);var j="appendChild",h="createElement",k="src",n=d[h]("div"),v=n[j](d[h](z)),b=d[h]("iframe"),g="document",e="domain",o;n.style.display="none";m.insertBefore(n,m.firstChild).id=z;b.frameBorder="0";b.id=z+"-loader";if(/MSIE[ ]+6/.test(navigator.userAgent)){
        b.src="javascript:false"}b.allowTransparency="true";v[j](b);try{
        b.contentWindow[g].open()}catch(w){
        c[e]=d[e];o="javascript:var d="+g+".open();d.domain='"+d.domain+"';";b[k]=o+"void(0);"}try{
        var t=b.contentWindow[g];t.write(p());t.close()}catch(x){
        b[k]=o+'d.write("'+p().replace(/"/g,String.fromCharCode(92)+'"')+'");d.close();'}a.P(2)};ld()};nt()})({
        loader: "static.olark.com/jsclient/loader0.js",name:"olark",methods:["configure","extend","declare","identify"]});
        $configuration
        olark.identify('$serviceId');/*]]>*/</script><noscript><a href="https://www.olark.com/site/$serviceId/contact" title="Contact us" target="_blank">Questions? Feedback?</a> powered by <a href="http://www.olark.com?welcome" title="Olark live chat software">Olark live chat software</a></noscript>
        <!-- end olark code -->
EOL;

        $script = ob_get_contents();
        ob_end_clean();

        return $script;
    }

    /**
     * Get the widget instance ID.
     * 
     * @return string
     */
    public function getInstanceId()
    {
        return $this->_instanceId;
    }

    /**
     * Get the widget HTML based on service plan.
     * 
     * @return string
     */
    public function getOlarkHtml()
    {
        $data   = $this->getWidgetData();
        $html   = '';

        if ($data->getOlarkWidgetMode() == 'custom') {
            $html .= $data->getOlarkCustomHtml();
        }

        switch ($data->getOlarkServicePlan()) {
            case 'ultimate':
                //break;
            case 'platinum':
                //break;
            case 'gold';
                //break;
            case 'bronze':
                //break;
            case 'free':
            default:
                $html .= $this->getInstallationScript();
                break;
        }

        return $html;
    }

    /**
     * Get the service plan options.
     * 
     * @return array
     */
    public function getServicePlans()
    {
        return array(
            array(
                'value' => '',
                'label' => $this->__('Select One'),
            ),
            array(
                'value' => 'free',
                'label' => $this->__('Free'),
            ),
            array(
                'value' => 'bronze',
                'label' => $this->__('Bronze'),
            ),
            array(
                'value' => 'gold',
                'label' => $this->__('Gold'),
            ),
            array(
                'value' => 'platinum',
                'label' => $this->__('Platinum'),
            ),
            array(
                'value' => 'ultimate',
                'label' => $this->__('Ultimate'),
            ),
        );
    }

    /**
     * Get the widget setup mode options.
     * 
     * @return array
     */
    public function getWidgetModes()
    {
        return array(
            array(
                'value' => '',
                'label' => $this->__('Default'),
            ),
            array(
                'value' => 'custom',
                'label' => $this->__('Custom'),
            ),
        );
    }

}