<?php
//this is a class in case the primary deal table is changed or a collection of
//different views are implemented and this can be extended to work with that.
class reportfrombuilder{
	public function build_fromclause(){
		return ' FROM deals d ';
	}
}