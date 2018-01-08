<ul class="nav nav-sidebar">
  <li class="{{ isset($active)&&$active=='branch'?'active':'' }}"><a href="/masterfiles/branch">Branch</a></li>
  <li class="{{ isset($active)&&$active=='company'?'active':'' }}"><a href="/masterfiles/company">Company</a></li>
  <li class="{{ isset($active)&&$active=='lessor'?'active':'' }}"><a href="#">Lessor</a></li>
  <li class="{{ isset($active)&&$active=='sector'?'active':'' }}"><a href="#">Sector</a></li>
  <li class="{{ isset($active)&&$active=='component'?'active':'' }}"><a href="/masterfiles/component">Component</a></li>
</ul>