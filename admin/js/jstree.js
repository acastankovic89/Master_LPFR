function selectRootNode($jstreeRoot) {
  $jstreeRoot.addClass('root-selected');
};

function deselectRootNode($jstreeRoot) {
  if($jstreeRoot.hasClass('root-selected')) {
    $jstreeRoot.removeClass('root-selected')
  }
};

function setSelectedNode(parentId, $jstree, $jstreeRoot) {

  if(parentId && parentId !== '0') {
    $jstree.jstree(true).select_node(parentId);
  } else {
    selectRootNode($jstreeRoot);
  }
};


/*************************************** Drag and drop (DND) ***************************************/

function handleDnDMenuTree(e, data) {

  var nodeId = data.node.id;
  var newParentId = data.parent;
  var newPosition = data.position;

  if (newParentId === '#') newParentId = 0;

  var payload = {
    id: nodeId,
    parent_id: newParentId,
    position: newPosition
  };

  var headers = {
    headers: {
      'Content-Type': 'application/json;charset=UTF-8',
      'Accept': 'application/json'
    },
    isAxiosError: false
  };

  axios.put(API_URL + '/menu_items/position', payload, headers)
    .then(function (response) {

      if(response.status === 200) {

        var id = response.data.id;

        if(domElemExists('.t-row-' + id)) {

          var $row = $('.t-row-' + id);
          var parentId = response.data.parent_id;
          var parentName = response.data.parent_name;
          if(!exists(parentName) || parentName == '') {
            parentName = '-';
          }

          $row.find('.edit-item').attr('data-parent_id', parentId);
          $row.find('td.parent-name').text(parentName);
        }
      }
    })
    .catch(function (error) {
      console.log(error);
    });
};

function handleDnDSliderItemsTree(e, data) {

  var nodeId = data.node.id;
  var newPosition = data.position;

  var payload = {
    id: nodeId,
    position: newPosition
  };

  var headers = {
    headers: {
      'Content-Type': 'application/json;charset=UTF-8',
      'Accept': 'application/json'
    },
    isAxiosError: false
  };

  axios.put(API_URL + '/slider_items/position', payload, headers)
    .then(function (response) {

      if(response.status === 200) {
        console.log(response);
      }
    })
    .catch(function (error) {
      console.log(error);
    });
};

/******************************************* /end of DND *******************************************/


/***************************************** Initialization *****************************************/

function initCategoriesTree() {

  if(domElemExists('.jstree')) {

    $('.jstree').each(function () {

      var $jstree = $(this);
      var $pageForm = $jstree.parents('.page-form');
      var $wrapper = $jstree.parents('.jstree-wrapper');
      var $jstreeRoot = $wrapper.find('.jstree-root');
      var $parentField = $pageForm.find('.item-parent-id');

      var itemId = $pageForm.find('.item-id').val();
      var parentId = $parentField.val();

      $jstree.jstree();

      setSelectedNode(parentId, $jstree, $jstreeRoot);

      $jstreeRoot.on('click',
        function (event) {
          eventPreventDefault(event);
          $jstree.jstree('deselect_all');

          selectRootNode($jstreeRoot);
          $parentField.val(0);
        }
      );

      $jstree.on('changed.jstree', function (e, data) {

        deselectRootNode($jstreeRoot);

        var id = data.selected[0];

        if (typeof id != 'undefined' && id != null) {

          if (domElemExists('#categoryInsertPage')) {

            if (id != itemId) {
              $parentField.val(id);
            } else {
              alert('Category cannot be its own parent');
              data.instance.deselect_node(data.node);
              selectRootNode($jstreeRoot);
            }
          } else {
            if (id != parentId) {
              $parentField.val(id);
            }
          }
        }
      });
    });
  }
};

function initMenuItemsTree() {

  if(domElemExists('.jstree')) {

    $('.jstree').each(function () {

      $(this).on('move_node.jstree', function (e, data) {
        handleDnDMenuTree(e, data);
      }).jstree({
        'core': {
          'check_callback': true
        },
        'plugins': ['dnd']
      });
      $(this).jstree(true).refresh();
    });
  }
};

function initSliderItemsTree() {

  if(domElemExists('.jstree')) {

    $('.jstree').each(function () {

      $(this).on('move_node.jstree', function (e, data) {
        handleDnDSliderItemsTree(e, data);
      }).jstree({
        'core': {
          'check_callback': true
        },
        'plugins': ['dnd']
      });
      $(this).jstree(true).refresh();
    });
  }
};

/************************************* /end of Initialization *************************************/