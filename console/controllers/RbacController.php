<?php
namespace console\Controllers;

use Yii;
use yii\rbac\Permission;
use yii\rbac\Role;

class RbacController extends \yii\console\Controller
{

    const ADMIN_ROLE_NAME = 'Admin';
    const USER_ROLE_NAME = 'User';

    protected $userRole;
    protected $adminRole;

    public function actionInit()
    {
        $this->actionAddCommonRoles();
        $this->actionAddRequestsPermissions();
        $this->actionAddRatesPermissions();
    }

    private function actionAddCommonRoles()
    {
        $this->addCommonUserRole();
        $this->addCommonAdminRole();
    }

    private function addCommonUserRole(array $permissions = [])
    {
        $auth = Yii::$app->authManager;

        //User role
        if (!$this->userRole = $auth->getRole(self::USER_ROLE_NAME)) {
            $this->userRole = $this->createRole(self::USER_ROLE_NAME, $permissions, 'User role');
        }
    }

    private function addCommonAdminRole(array $permissions = [])
    {
        $auth = Yii::$app->authManager;

        //Admin role
        if (!$this->adminRole = $auth->getRole(self::ADMIN_ROLE_NAME)) {
            $this->adminRole = $this->createRole(self::ADMIN_ROLE_NAME, $permissions, 'Admin role' );
            $auth->assign($this->adminRole, 1); // User with ID 1 has Admin role;
        }
    }

    public function actionAddRequestsPermissions()
    {
        //getRequestsList permission
        $getRequestsList = $this->createPermission('getRequestsList', 'Get Requests list permission');

        //getRequestDetails permission
        $getRequestDetails = $this->createPermission('getRequestDetails', 'Get Request details permission');

        //createRequest permission
        $createRequestDetails = $this->createPermission('createRequestDetails', 'Create Request details permission');

        //updateRequest permission
        $updateRequestDetails = $this->createPermission('updateRequestDetails', 'Update Request details permission');

        //deleteRequest permission
        $deleteRequestDetails = $this->createPermission('deleteRequestDetails', 'Delete Request details permission');

        //requestsUser role
        $requestsUser = $this->createRole('requestsUser', [
            $getRequestDetails,
            $createRequestDetails,
            ], 'Requests user role');

        //requestsAdmin role
        $requestsAdmin = $this->createRole('requestsAdmin', [
            $getRequestsList,
            $updateRequestDetails,
            $deleteRequestDetails,
            ], 'Requests admin role');

        $this->addRoleChild($requestsAdmin, $requestsUser);

        //User role
        $this->addCommonUserRole();
        $this->addRoleChild($this->userRole, $requestsUser);

        //Admin role
        $this->addCommonAdminRole();
        $this->addRoleChild($this->adminRole, $requestsAdmin);
    }

    public function actionAddRatesPermissions()
    {
        //getRatesList permission
        $getRatesList = $this->createPermission('getRatesList', 'Get Rates list permission');

        //getRateDetails permission
        $getRateDetails = $this->createPermission('getRateDetails', 'Get Rate details permission');

        //createRate permission
        $createRateDetails = $this->createPermission('createRateDetails', 'Create Rate details permission');

        //updateRate permission
        $updateRateDetails = $this->createPermission('updateRateDetails', 'Update Rate details permission');

        //deleteRate permission
        $deleteRateDetails = $this->createPermission('deleteRateDetails', 'Delete Rate details permission');

        //ratesUser role
        $ratesUser = $this->createRole('ratesUser', [
            $getRateDetails,
        ], 'Rates user role');

        //ratesAdmin role
        $ratesAdmin = $this->createRole('ratesAdmin', [
            $getRatesList,
            $createRateDetails,
            $updateRateDetails,
            $deleteRateDetails,
        ], 'Rates admin role');

        $this->addRoleChild($ratesAdmin, $ratesUser);

        //User role
        $this->addCommonUserRole();
        $this->addRoleChild($this->userRole, $ratesUser);

        //Admin role
        $this->addCommonAdminRole();
        $this->addRoleChild($this->adminRole, $ratesAdmin);
    }

    private function createPermission($permissionName, $permissionDesc = '')
    {
        $auth = Yii::$app->authManager;

        if ($permission = $auth->getPermission($permissionName)) {

        } else {
            $permissionDesc = empty($permissionDesc) ? $permissionName : $permissionDesc;

            $permission = $auth->createPermission($permissionName);
            $permission->description = $permissionDesc;
            if ($auth->add($permission)) {
                $this->stdout($permission->description . " has been added\n");
            }
        }
        return $permission;
    }

    /**
     * @param $roleName
     * @param array $permissions
     * @param string $roleDesc
     * @return \yii\rbac\Role
     */
    private function createRole($roleName, array $permissions, $roleDesc = '')
    {
        $auth = Yii::$app->authManager;

        if ($role = $auth->getRole($roleName)) {
            $this->addRolePermissions($role, $permissions);
        } else {
            $roleDesc = empty($roleDesc) ? $roleName : $roleDesc;

            $role = $auth->createRole($roleName);
            $role->description = $roleDesc;
            if ($auth->add($role)) {
                $this->stdout($role->description . " has been added\n");
                $this->addRolePermissions($role, $permissions);
            }
        }
        return $role;
    }

    private function addRolePermissions(Role $role, array $permissions)
    {
        $auth = Yii::$app->authManager;

        foreach ($permissions as $permission) {
            if (!$this->checkRolePermission($role, $permission)) {
                $auth->addChild($role, $permission);
            }
        }
    }

    private function addRoleChild(Role $role, $child)
    {
        $auth = Yii::$app->authManager;

        if (!$this->checkRoleChild($role, $child)) {
            $auth->addChild($role, $child);
        }

    }

    private function checkRolePermission(Role $role, Permission $permission)
    {
        $auth = Yii::$app->authManager;

        $rolePermissions = $auth->getPermissionsByRole($role->name);

        return in_array($permission, $rolePermissions);
    }

    private function checkRoleChild(Role $role, $child)
    {
        $auth = Yii::$app->authManager;

        $roleChildren = $auth->getChildren($role->name);

        return in_array($child, $roleChildren);
    }

    public function actionRemoveAll()
    {
        $auth = Yii::$app->authManager;

        $auth->removeAll();

        $this->stdout("All permissions and roles have been removed\n");
    }
}