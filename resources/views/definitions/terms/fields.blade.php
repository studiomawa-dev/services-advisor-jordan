@section('scripts')
<script>
	var termsJson = JSON.parse('<?= json_encode($terms, JSON_HEX_APOS) ?>');
	var parentId = <?= $term->parent_id ?>;
	var termId = <?= $term->id ?>;


	$(function() {
		initCategories();
	});


	function initCategories() {
		var selectContainer = $('#category-select-container');
		var servicesSelect = $('#category-select');

		var categories = [];

		for (var i = 0; i < termsJson.length; i++) {
			var category = termsJson[i];
			category.children = termsJson.filter(item => {
				return item.parent_id == category.id;
			});

			if (category.children.length > 0) {
				for (var j = 0; j < category.children.length; j++) {
					var child = category.children[j];
					child.parent = category;
				}
			}

			categories.push(category);
		}

		var onCategorySelect = function() {
			var selectedCategory = null;
			var selectedVal = parseInt($(this).val());
			var selectIndex = parseInt($(this).data('index'));
			var parentCatId = parseInt($(this).data('parent-id'));
			var cats = categories.filter(item => {
				return item.id == selectedVal;
			});

			if (cats.length == 1) {
				selectedCategory = cats[0];
			} else if (cats.length == 0) {
				if (isNaN(parentCatId)) {
					$('input[name="parent_id"]').val(0);
				} else {
					$('input[name="parent_id"]').val(parentCatId);
				}
			}
			removeOtherSelects(selectIndex);

			if (selectedCategory != null) {
				fillSelect(selectIndex, selectedCategory);
			}
		}

		var fillSelect = function(index, category) {
			if (category.children.length > 0) {
				var newIndex = index + 1;
				selectContainer.append('<select id="category-select-' + newIndex + '" class="category-select form-control mb-2" data-parent-id="' + category.id + '" data-index="' + (index + 1) + '"></select>');
				var select = $('#category-select-' + newIndex);
				select.on('change', onCategorySelect);


				select.append('<option></option>');
				for (var i = 0; i < category.children.length; i++) {
					var item = category.children[i];
					if (item.id == termId) continue;
					select.append('<option value="' + item.id + '">' + item.name + '</option>');

					//if (i == 0 && item.children.length > 0) {
					//fillSelect(newIndex, item);
					//}
				}
			}

			$('input[name="parent_id"]').val(category.id);
		}

		var removeOtherSelects = function(index) {
			var selects = $('.category-select');
			if (selects.length > 0) {
				for (var i = 0; i < selects.length; i++) {
					var select = selects[i];
					var selectIndex = parseInt($(select).data('index'));
					if (selectIndex > index) {
						$(select).remove();
					}
				}
			}
		}

		var selectCategoryById = function(selectedCategoryId) {
			var selectedCategories = categories.filter(x => x.id == selectedCategoryId);
			var selectedCategory = (selectedCategories.length > 0) ? selectedCategories[0] : null;
			var categoriesList = [];

			if (selectedCategory) {
				var currentCategory = selectedCategory;
				categoriesList.unshift(currentCategory);

				while (currentCategory.parent != null) {
					currentCategory = currentCategory.parent;
					categoriesList.unshift(currentCategory);
				}
			}


			if (categoriesList.length > 0) {
				for (var i = 0; i < categoriesList.length; i++) {
					fillSelect(i, categoriesList[i]);
					var select = $('#category-select' + (i == 0 ? '' : '-' + i));
					select.val(categoriesList[i].id);
				}
			}
		}

		var rootCategories = categories.filter(item => {
			return item.parent_id == 0;
		});

		servicesSelect.empty();
		servicesSelect.append($("<option />").val(null).text('<?= __('app.Root Category') ?>'))

		servicesSelect.on('change', onCategorySelect);


		console.log(rootCategories);

		$.each(rootCategories, function() {
			servicesSelect.append($("<option />").val(this.id).text(this.name))
		});

		if (parentId > 0) {
			selectCategoryById(parentId);
		}
	}
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.3/js/bootstrap-colorpicker.min.js"></script>
<script>
	$('.colorpicker').colorpicker();
</script>
@endsection

<div class="col-md-12">
	<div class="row">

		@if ($term->id > 0)
		<div class="form-group col-sm-12">
			{!! Form::label('taxonomy_id', 'Code:') !!}
			<div>{!! $term->id !!}</div>
			{!! Form::hidden('taxonomy_id', $term->taxonomy_id) !!}
		</div>
		@endif

		<!-- Taxonomy Id Field -->
		<div class="form-group col-sm-12">
			{!! Form::label('taxonomy_id', 'Taxonomy:') !!}
			{!! Form::select('taxonomy_id', $taxonomies, null, ['class' => 'form-control', 'disabled'=>'disabled']) !!}
			{!! Form::hidden('taxonomy_id', $term->taxonomy_id) !!}
		</div>


		<div class="form-group col-sm-12">
			{!! Form::label('parent_id', 'Parent:') !!}

			<div class="row">
				<div class="col-md-12 mb-2">
					<select id="category-select" class="form-control" data-index="0"></select>
				</div>
				<div id="category-select-container" class="col-md-12"></div>
			</div>
		</div>
		{!! Form::hidden('parent_id', 0) !!}




		@if($term->parent_id == 0)
		<div class="form-group col-sm-12">
			{!! Form::label('color', 'Color:') !!}

			<div class="row">
				<div class="col-md-2 mb-2">
					{!! Form::text('color', null, ['class' => 'form-control colorpicker']) !!}
				</div>
			</div>
		</div>
		@endif


	</div>
</div>

<div class="col-sm-12 mt-3">
	<nav>
		<div class="nav nav-tabs" id="nav-tab" role="tablist">
			@foreach($langs as $lang)
			<a class="nav-item nav-link {{ $loop->index == 0 ? 'active' : '' }}" id="lang-{{ $lang->id }}-tab" data-toggle="tab" href="#lang-{{ $lang->id }}" role="tab" aria-controls="lang-{{ $lang->id }}"" aria-selected=" false">{{ $lang->name }}</a>
			@endforeach
		</div>
	</nav>
	<div class="tab-content">
		@foreach($langs as $lang)
		<div class="tab-pane fade {{ $loop->index == 0 ? 'show active' : '' }}" id="lang-{{ $lang->id }}" role="tabpanel" aria-labelledby="lang-{{ $lang->id }}-tab">
			<div class="row">
				<div class="form-group col-sm-12 mt-3">
					{!! Form::label('langs[l'.$lang->id.'][name]', 'Title:') !!}
					{!! Form::text('langs[l'.$lang->id.'][name]', null, ['class' => 'form-control']) !!}
				</div>
			</div>
		</div>
		@endforeach
	</div>
</div>

{!! Form::hidden('deleted', 0) !!}
{!! Form::hidden('order', 0) !!}

<!-- Submit Field -->
<div class="form-group col-sm-12">
	{!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
	<a href="{!! route('definitions.terms.index', ['taxonomy' => $term->taxonomy_id]) !!}" class="btn btn-default">Cancel</a>
</div>